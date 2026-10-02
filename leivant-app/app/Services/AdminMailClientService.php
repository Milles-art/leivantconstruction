<?php

namespace App\Services;

use App\Models\AdminMailAccount;
use App\Models\AdminMailAttachment;
use App\Models\AdminMailContact;
use App\Models\AdminMailMessage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Throwable;

class AdminMailClientService
{
    public function testConnection(array $settings): void
    {
        if (! extension_loaded('imap') || ! function_exists('imap_open')) {
            throw new \RuntimeException('IMAP is not enabled on this server.');
        }

        $mailbox = $this->mailboxString($settings['imap_host'], (int) $settings['imap_port'], $settings['imap_encryption'] ?? 'ssl', 'INBOX');
        $imap = @imap_open($mailbox, $settings['username'], $settings['password']);
        if (! $imap) {
            $error = imap_last_error();
            $this->clearImapErrors();
            throw new \RuntimeException('IMAP failed: '.$error);
        }
        imap_close($imap);

        $transport = $this->transport(
            $settings['smtp_host'],
            (int) $settings['smtp_port'],
            $settings['smtp_encryption'] ?? 'ssl',
            $settings['username'],
            $settings['password']
        );
        $transport->start();
        $transport->stop();
    }

    public function syncActiveAccounts(): array
    {
        $results = [];
        AdminMailAccount::query()->where('is_active', true)->each(function (AdminMailAccount $account) use (&$results) {
            try {
                $results[$account->email] = $this->syncAccount($account);
                $account->forceFill(['last_synced_at' => now(), 'last_sync_error' => null])->save();
            } catch (Throwable $exception) {
                $account->forceFill(['last_sync_error' => $exception->getMessage()])->save();
                $results[$account->email] = 'failed: '.$exception->getMessage();
            }
        });

        return $results;
    }

    public function syncAccount(AdminMailAccount $account): int
    {
        $imported = 0;

        foreach ($account->folders() as $folderKey => $folderName) {
            $imap = $this->openAccount($account, $folderName, $folderKey !== 'inbox');
            if (! $imap) {
                $this->clearImapErrors();
                continue;
            }

            try {
                $scanLimit = $folderKey === 'inbox' ? 60 : 30;
                $numbers = array_slice(array_reverse(imap_search($imap, 'ALL') ?: []), 0, $scanLimit);
                foreach ($numbers as $number) {
                    $overview = imap_fetch_overview($imap, (string) $number, 0)[0] ?? null;
                    if (! $overview) {
                        continue;
                    }
                    $uid = (string) imap_uid($imap, $number);
                    $messageId = isset($overview->message_id) ? trim((string) $overview->message_id) : null;
                    $direction = in_array($folderKey, ['sent', 'drafts'], true) ? 'outbound' : 'inbound';
                    $message = AdminMailMessage::query()
                        ->where('account_id', $account->id)
                        ->where('folder', $folderKey)
                        ->where('uid', $uid)
                        ->first();

                    if (! $message && $messageId) {
                        $message = AdminMailMessage::query()
                            ->where('account_id', $account->id)
                            ->where('direction', $direction)
                            ->where('message_id', $messageId)
                            ->first();
                    }

                    $header = imap_headerinfo($imap, $number);
                    $from = $header->from[0] ?? null;
                    $bodyText = $message ? $message->body_text : $this->fetchBody($imap, $number, 'plain');
                    $bodyHtml = $message ? $message->body_html : $this->fetchBody($imap, $number, 'html');
                    $payload = [
                        'account_id' => $account->id,
                        'uid' => $uid,
                        'message_id' => $messageId,
                        'direction' => $direction,
                        'mailbox' => $folderName,
                        'folder' => $folderKey,
                        'from_email' => $from ? $this->emailFromAddress($from) : $account->email,
                        'from_name' => $from && isset($from->personal) ? $this->decodeMime((string) $from->personal) : null,
                        'to_email' => $this->addressList($header->to ?? []),
                        'cc_email' => $this->addressList($header->cc ?? []),
                        'subject' => $this->decodeMime((string) ($overview->subject ?? '(No subject)')),
                        'body_text' => $bodyText,
                        'body_html' => $bodyHtml,
                        'received_at' => isset($overview->date) ? date('Y-m-d H:i:s', strtotime($overview->date)) : now(),
                        'sent_at' => $folderKey === 'sent' ? (isset($overview->date) ? date('Y-m-d H:i:s', strtotime($overview->date)) : now()) : null,
                        'is_read' => ! empty($overview->seen),
                        'is_draft' => $folderKey === 'drafts',
                        'status' => $folderKey === 'sent' ? 'sent' : ($folderKey === 'drafts' ? 'draft' : 'open'),
                        'flags_json' => json_encode([
                            'answered' => ! empty($overview->answered),
                            'flagged' => ! empty($overview->flagged),
                            'deleted' => ! empty($overview->deleted),
                            'draft' => ! empty($overview->draft),
                        ]),
                    ];

                    if ($message) {
                        $message->fill([
                            'uid' => $payload['uid'],
                            'message_id' => $payload['message_id'] ?: $message->message_id,
                            'direction' => $payload['direction'],
                            'folder' => $folderKey,
                            'is_read' => $payload['is_read'],
                            'flags_json' => $payload['flags_json'],
                            'mailbox' => $folderName,
                            'status' => $payload['status'],
                        ])->save();
                    } else {
                        $message = AdminMailMessage::query()->create($payload);
                        $this->importAttachments($imap, (int) $number, $message);
                        $this->applyRules($message);
                        $imported++;
                    }

                    $this->rememberContacts($account, $payload['from_email'], $payload['from_name'], 'received');
                    $this->rememberAddressList($account, $payload['to_email'], 'received');
                    $this->rememberAddressList($account, $payload['cc_email'] ?? '', 'received');
                }
            } finally {
                imap_close($imap);
            }
        }

        return $imported;
    }

    public function send(AdminMailAccount $account, array $payload, array $storedFiles = []): string
    {
        $email = (new Email())
            ->from(new Address($account->email, $account->display_name ?: $account->email))
            ->to(...$this->addresses($payload['to_email']))
            ->subject($payload['subject']);

        if (! empty($payload['cc_email'])) {
            $email->cc(...$this->addresses($payload['cc_email']));
        }
        if (! empty($payload['bcc_email'])) {
            $email->bcc(...$this->addresses($payload['bcc_email']));
        }

        $html = $payload['body_html'] ?: nl2br(e($payload['body_text'] ?? ''));
        if ($account->signature_html && empty($payload['skip_signature'])) {
            $html .= '<br><br>'.$account->signature_html;
        }
        $email->html($html)->text(trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html))));

        foreach ($storedFiles as $file) {
            $email->attachFromPath(Storage::path($file['path']), $file['original_name'], $file['mime_type'] ?: null);
        }

        $mailer = new Mailer($this->transport($account->smtp_host, (int) $account->smtp_port, $account->smtp_encryption, $account->username, $account->plainPassword()));
        $mailer->send($email);

        $raw = $email->toString();
        $this->appendRaw($account, 'sent', $raw);

        return $raw;
    }

    public function saveDraft(AdminMailAccount $account, AdminMailMessage $draft): void
    {
        $html = $draft->body_html ?: nl2br(e($draft->body_text ?? ''));
        $email = (new Email())
            ->from(new Address($account->email, $account->display_name ?: $account->email))
            ->subject($draft->subject ?: '(No subject)')
            ->html($html)
            ->text(trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html))));

        if ($draft->to_email) {
            $email->to(...$this->addresses($draft->to_email));
        }
        if ($draft->cc_email) {
            $email->cc(...$this->addresses($draft->cc_email));
        }
        if ($draft->bcc_email) {
            $email->bcc(...$this->addresses($draft->bcc_email));
        }

        $this->appendRaw($account, 'drafts', $email->toString(), '\\Draft');
    }

    public function moveMessage(AdminMailMessage $message, string $targetFolderKey): void
    {
        if (! $message->account || ! $message->uid) {
            $target = $message->account?->folderFor($targetFolderKey) ?? $targetFolderKey;
            $this->updateMovedMessageState($message, $targetFolderKey, $target, null);
            return;
        }

        $source = $message->mailbox ?: $message->account->folderFor($message->folder ?: 'inbox');
        $target = $message->account->folderFor($targetFolderKey);
        $imap = $this->openAccount($message->account, $source, false);
        if (! $imap) {
            throw new \RuntimeException('Could not open source folder.');
        }
        try {
            $this->ensureFolder($message->account, $targetFolderKey);
            if (! imap_mail_move($imap, (string) $message->uid, $target, CP_UID)) {
                throw new \RuntimeException(imap_last_error() ?: 'Move failed.');
            }
            imap_expunge($imap);
        } finally {
            imap_close($imap);
        }

        $this->updateMovedMessageState(
            $message,
            $targetFolderKey,
            $target,
            $this->findUidByMessageId($message->account, $target, $message->message_id)
        );
    }

    private function updateMovedMessageState(AdminMailMessage $message, string $targetFolderKey, string $targetMailbox, ?string $targetUid): void
    {
        if ($targetUid) {
            $duplicate = AdminMailMessage::query()
                ->where('mailbox', $targetMailbox)
                ->where('uid', $targetUid)
                ->where('id', '!=', $message->id)
                ->first();

            if ($duplicate) {
                $sameAccount = (int) $duplicate->account_id === (int) $message->account_id;
                $sameMessage = $this->sameImportedMessage($message, $duplicate);

                if ($sameAccount && $sameMessage) {
                    $this->mergeMovedDuplicate($message, $duplicate);
                } else {
                    $targetUid = null;
                }
            }
        }

        $message->update([
            'folder' => $targetFolderKey,
            'mailbox' => $targetMailbox,
            'uid' => $targetUid,
        ]);
    }

    private function sameImportedMessage(AdminMailMessage $message, AdminMailMessage $duplicate): bool
    {
        $messageId = trim((string) $message->message_id);
        $duplicateMessageId = trim((string) $duplicate->message_id);

        if ($messageId !== '' && $duplicateMessageId !== '') {
            return $messageId === $duplicateMessageId;
        }

        return strtolower((string) $message->subject) === strtolower((string) $duplicate->subject)
            && strtolower((string) $message->from_email) === strtolower((string) $duplicate->from_email)
            && (string) optional($message->received_at)->toDateTimeString() === (string) optional($duplicate->received_at)->toDateTimeString();
    }

    private function mergeMovedDuplicate(AdminMailMessage $message, AdminMailMessage $duplicate): void
    {
        DB::table('admin_mail_attachments')
            ->where('admin_mail_message_id', $duplicate->id)
            ->update(['admin_mail_message_id' => $message->id]);

        DB::table('admin_mail_replies')
            ->where('admin_mail_message_id', $duplicate->id)
            ->update(['admin_mail_message_id' => $message->id]);

        AdminMailMessage::query()
            ->where('parent_id', $duplicate->id)
            ->update(['parent_id' => $message->id]);

        $message->forceFill([
            'has_attachments' => $message->has_attachments || $duplicate->has_attachments,
            'is_read' => $message->is_read || $duplicate->is_read,
            'flags_json' => $message->flags_json ?: $duplicate->flags_json,
        ])->save();

        $duplicate->delete();
    }

    public function setSeen(AdminMailMessage $message, bool $seen): void
    {
        if ($message->account && $message->uid) {
            $imap = $this->openAccount($message->account, $message->mailbox ?: $message->account->folderFor($message->folder ?: 'inbox'), false);
            if ($imap) {
                try {
                    $seen ? imap_setflag_full($imap, (string) $message->uid, '\\Seen', ST_UID) : imap_clearflag_full($imap, (string) $message->uid, '\\Seen', ST_UID);
                } finally {
                    imap_close($imap);
                }
            }
        }
        $message->update(['is_read' => $seen]);
    }

    public function storeUploadedFiles(array $files): array
    {
        $stored = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $stored[] = [
                    'path' => $file->store('admin-mail-attachments/outbound/'.date('Y/m')),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }
        return $stored;
    }

    public function ensureFolder(AdminMailAccount $account, string $folderKey): void
    {
        if ($folderKey === 'inbox') {
            return;
        }
        $imap = @imap_open($this->mailboxString($account->imap_host, (int) $account->imap_port, $account->imap_encryption, 'INBOX'), $account->username, $account->plainPassword());
        if (! $imap) {
            return;
        }
        try {
            $folder = $this->mailboxString($account->imap_host, (int) $account->imap_port, $account->imap_encryption, $account->folderFor($folderKey));
            @imap_createmailbox($imap, $this->encodeMailbox($folder));
            $this->clearImapErrors();
        } finally {
            imap_close($imap);
        }
    }

    private function openAccount(AdminMailAccount $account, string $folderName, bool $createIfMissing = false)
    {
        $imap = @imap_open($this->mailboxString($account->imap_host, (int) $account->imap_port, $account->imap_encryption, $folderName), $account->username, $account->plainPassword());
        if (! $imap && $createIfMissing) {
            $this->ensureFolder($account, array_search($folderName, $account->folders(), true) ?: 'archive');
            $imap = @imap_open($this->mailboxString($account->imap_host, (int) $account->imap_port, $account->imap_encryption, $folderName), $account->username, $account->plainPassword());
        }
        if (! $imap) {
            $this->clearImapErrors();
        }
        return $imap;
    }

    private function appendRaw(AdminMailAccount $account, string $folderKey, string $raw, string $flags = ''): void
    {
        $this->ensureFolder($account, $folderKey);
        $imap = @imap_open($this->mailboxString($account->imap_host, (int) $account->imap_port, $account->imap_encryption, 'INBOX'), $account->username, $account->plainPassword());
        if (! $imap) {
            return;
        }
        try {
            @imap_append($imap, $this->mailboxString($account->imap_host, (int) $account->imap_port, $account->imap_encryption, $account->folderFor($folderKey)), $raw, $flags);
            $this->clearImapErrors();
        } finally {
            imap_close($imap);
        }
    }

    private function findUidByMessageId(AdminMailAccount $account, string $folderName, ?string $messageId): ?string
    {
        if (! $messageId) {
            return null;
        }

        $imap = $this->openAccount($account, $folderName, false);
        if (! $imap) {
            return null;
        }

        try {
            $numbers = array_slice(array_reverse(imap_search($imap, 'ALL') ?: []), 0, 80);
            foreach ($numbers as $number) {
                $overview = imap_fetch_overview($imap, (string) $number, 0)[0] ?? null;
                if ($overview && isset($overview->message_id) && trim((string) $overview->message_id) === trim($messageId)) {
                    return (string) imap_uid($imap, (int) $number);
                }
            }
        } finally {
            imap_close($imap);
        }

        return null;
    }

    private function clearImapErrors(): void
    {
        if (function_exists('imap_errors')) {
            imap_errors();
        }
        if (function_exists('imap_alerts')) {
            imap_alerts();
        }
    }

    private function transport(string $host, int $port, string $encryption, string $username, string $password): EsmtpTransport
    {
        $transport = new EsmtpTransport($host, $port, $encryption === 'ssl');
        $transport->setUsername($username);
        $transport->setPassword($password);
        return $transport;
    }

    private function mailboxString(string $host, int $port, string $encryption, string $folder): string
    {
        $flags = $encryption === 'ssl' ? '/imap/ssl/novalidate-cert' : '/imap/notls';
        return sprintf('{%s:%d%s}%s', $host, $port, $flags, $folder);
    }

    private function encodeMailbox(string $mailbox): string
    {
        return function_exists('imap_utf7_encode') ? imap_utf7_encode($mailbox) : $mailbox;
    }

    private function fetchBody($imap, int $number, string $preferred): ?string
    {
        $structure = imap_fetchstructure($imap, $number);
        $part = $this->findPart($structure, $preferred);
        if ($part) {
            [$section, $encoding] = $part;
            return $this->decodeBody(imap_fetchbody($imap, $number, $section), $encoding);
        }
        $body = imap_body($imap, $number);
        return $body ? trim(quoted_printable_decode($body)) : null;
    }

    private function findPart($structure, string $preferred, string $prefix = ''): ?array
    {
        if (! isset($structure->parts)) {
            $subtype = strtolower((string) ($structure->subtype ?? ''));
            if (($preferred === 'plain' && $subtype === 'plain') || ($preferred === 'html' && $subtype === 'html')) {
                return [$prefix ?: '1', (int) ($structure->encoding ?? 0)];
            }
            return null;
        }
        foreach ($structure->parts as $index => $part) {
            $section = $prefix === '' ? (string) ($index + 1) : $prefix.'.'.($index + 1);
            $subtype = strtolower((string) ($part->subtype ?? ''));
            if (($preferred === 'plain' && $subtype === 'plain') || ($preferred === 'html' && $subtype === 'html')) {
                return [$section, (int) ($part->encoding ?? 0)];
            }
            if ($nested = $this->findPart($part, $preferred, $section)) {
                return $nested;
            }
        }
        return null;
    }

    private function importAttachments($imap, int $number, AdminMailMessage $message): void
    {
        $structure = imap_fetchstructure($imap, $number);
        if (! $structure || ! isset($structure->parts)) {
            return;
        }
        $attachments = [];
        $this->walkAttachments($imap, $number, $structure, '', $attachments);
        foreach ($attachments as $attachment) {
            $path = 'admin-mail-attachments/inbound/'.date('Y/m').'/'.uniqid('mail_', true).'_'.$attachment['name'];
            Storage::put($path, $attachment['content']);
            AdminMailAttachment::query()->create([
                'admin_mail_message_id' => $message->id,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $attachment['name'],
                'mime_type' => $attachment['mime'],
                'size' => strlen($attachment['content']),
            ]);
        }
        if ($attachments) {
            $message->update(['has_attachments' => true]);
        }
    }

    private function walkAttachments($imap, int $number, $structure, string $prefix, array &$attachments): void
    {
        foreach ($structure->parts ?? [] as $index => $part) {
            $section = $prefix === '' ? (string) ($index + 1) : $prefix.'.'.($index + 1);
            $filename = null;
            foreach (array_merge($part->dparameters ?? [], $part->parameters ?? []) as $param) {
                if (in_array(strtolower((string) $param->attribute), ['filename', 'name'], true)) {
                    $filename = $this->decodeMime((string) $param->value);
                }
            }
            $isAttachment = strtolower((string) ($part->disposition ?? '')) === 'attachment' || $filename;
            if ($isAttachment && $filename) {
                $content = $this->decodeBody(imap_fetchbody($imap, $number, $section), (int) ($part->encoding ?? 0));
                $attachments[] = [
                    'name' => preg_replace('/[^A-Za-z0-9._-]/', '_', $filename),
                    'mime' => strtolower((string) ($part->subtype ?? 'octet-stream')),
                    'content' => $content,
                ];
            }
            if (isset($part->parts)) {
                $this->walkAttachments($imap, $number, $part, $section, $attachments);
            }
        }
    }

    private function decodeBody(string $body, int $encoding): string
    {
        return match ($encoding) {
            3 => base64_decode($body) ?: $body,
            4 => quoted_printable_decode($body),
            default => $body,
        };
    }

    private function decodeMime(string $value): string
    {
        $decoded = function_exists('imap_mime_header_decode') ? imap_mime_header_decode($value) : false;
        return $decoded ? trim(collect($decoded)->map(fn ($part) => $part->text)->implode('')) : $value;
    }

    private function emailFromAddress(object $address): string
    {
        return strtolower(($address->mailbox ?? 'unknown').'@'.($address->host ?? 'example.com'));
    }

    private function addressList(array $addresses): string
    {
        return collect($addresses)->map(fn ($item) => $this->formatAddress($item))->filter()->implode(', ');
    }

    private function formatAddress(object $address): string
    {
        $email = $this->emailFromAddress($address);
        return ! empty($address->personal) ? $this->decodeMime((string) $address->personal).' <'.$email.'>' : $email;
    }

    private function addresses(string $emails): array
    {
        return array_map(fn ($email) => new Address($email), $this->splitEmails($emails));
    }

    public function splitEmails(?string $emails): array
    {
        if (! $emails) {
            return [];
        }
        return array_values(array_unique(array_filter(array_map(function ($email) {
            $email = trim($email);
            if (preg_match('/<([^>]+)>/', $email, $matches)) {
                return trim($matches[1]);
            }
            return $email;
        }, preg_split('/[,;]+/', $emails)))));
    }

    private function rememberAddressList(AdminMailAccount $account, ?string $addresses, string $source): void
    {
        foreach ($this->splitEmails($addresses) as $email) {
            $this->rememberContacts($account, $email, null, $source);
        }
    }

    private function rememberContacts(AdminMailAccount $account, string $email, ?string $name, string $source): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        AdminMailContact::query()->updateOrCreate(
            ['account_id' => $account->id, 'email' => strtolower($email)],
            ['name' => $name, 'source' => $source, 'last_seen_at' => now()]
        );
    }

    private function applyRules(AdminMailMessage $message): void
    {
        foreach ($message->account?->rules()->where('is_active', true)->get() ?? [] as $rule) {
            $haystack = match ($rule->field) {
                'sender' => $message->from_email,
                'subject' => $message->subject,
                default => $message->body_text ?: strip_tags((string) $message->body_html),
            };
            if (! str_contains(strtolower((string) $haystack), strtolower((string) $rule->value))) {
                continue;
            }
            if ($rule->action === 'move' && $rule->target_folder) {
                $message->fill(['folder' => $rule->target_folder, 'mailbox' => $message->account->folderFor($rule->target_folder)])->save();
            }
            if ($rule->action === 'mark_read') {
                $message->fill(['is_read' => true])->save();
            }
            if ($rule->action === 'assign' && $rule->assign_user_id) {
                $message->fill(['assigned_to_user_id' => $rule->assign_user_id])->save();
            }
        }
    }
}