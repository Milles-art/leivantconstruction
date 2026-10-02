<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminMailAccount;
use App\Models\AdminMailAttachment;
use App\Models\AdminMailContact;
use App\Models\AdminMailMessage;
use App\Models\AdminMailReply;
use App\Models\AdminMailRule;
use App\Models\User;
use App\Services\AdminMailClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MailInboxController extends Controller
{
    public function __construct(private AdminMailClientService $mailClient)
    {
    }

    public function index(Request $request): View
    {
        $accounts = $this->availableAccounts();
        $account = $this->selectedAccount($request, $accounts);
        $folder = $request->string('folder')->toString() ?: 'inbox';

        $messages = AdminMailMessage::query()
            ->select(['id', 'account_id', 'direction', 'mailbox', 'folder', 'from_email', 'from_name', 'to_email', 'subject', 'status', 'is_read', 'has_attachments', 'received_at', 'sent_at', 'created_at'])
            ->selectRaw('LEFT(COALESCE(body_text, body_html, ""), 700) as body_preview')
            ->with(['assignee:id,name', 'account:id,email,display_name'])
            ->when($account, fn ($query) => $query->where('account_id', $account->id))
            ->when(! $account, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($folder === 'failed', fn ($query) => $query->where('status', 'failed'))
            ->when($folder !== 'failed' && $folder !== 'all', fn ($query) => $query->where('folder', $folder))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->string('search'));
                $query->where(function ($query) use ($search) {
                    $query->where('subject', 'like', "%{$search}%")
                        ->orWhere('from_email', 'like', "%{$search}%")
                        ->orWhere('to_email', 'like', "%{$search}%")
                        ->orWhere('from_name', 'like', "%{$search}%")
                        ->orWhere('body_text', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByRaw('COALESCE(received_at, sent_at, created_at) DESC')
            ->paginate(12)
            ->withQueryString();

        return view('admin.mail-inbox.index', [
            'accounts' => $accounts,
            'account' => $account,
            'folder' => $folder,
            'messages' => $messages,
            'folders' => $this->folderCounts($account),
            'canManageAccounts' => $this->canManageAccounts(),
        ]);
    }

    public function live(Request $request): JsonResponse
    {
        $accounts = $this->availableAccounts();
        $account = $this->selectedAccount($request, $accounts);

        if (! $account) {
            return response()->json([
                'ok' => false,
                'message' => 'No mailbox connected.',
            ], 404);
        }

        $synced = false;
        $newMessages = 0;
        $account = $account->fresh();

        $folder = $request->string('folder')->toString() ?: 'inbox';
        $messages = $this->liveMessagesQuery($request, $account, $folder)
            ->limit(12)
            ->get()
            ->map(fn (AdminMailMessage $message) => $this->liveMessagePayload($message))
            ->values();

        $folders = $this->folderCounts($account);

        return response()->json([
            'ok' => true,
            'synced' => $synced,
            'new_messages' => $newMessages,
            'last_sync' => $account->last_synced_at?->diffForHumans() ?: 'waiting',
            'last_sync_error' => $account->last_sync_error,
            'counts' => $folders,
            'unread' => $folders['unread'] ?? 0,
            'total' => $folders['all'] ?? 0,
            'messages' => $messages,
        ]);
    }

    public function accounts(): View
    {
        abort_unless($this->canManageAccounts(), 403);

        return view('admin.mail-inbox.accounts', [
            'accounts' => AdminMailAccount::query()->with('user')->orderBy('email')->get(),
            'admins' => User::query()->where('is_admin', true)->orderBy('name')->get(),
            'folders' => AdminMailAccount::defaultFolders(),
        ]);
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        abort_unless($this->canManageAccounts(), 403);

        $validated = $this->validateAccount($request);
        $this->mailClient->testConnection($validated);

        $account = new AdminMailAccount($this->accountPayload($validated));
        $account->setPlainPassword($validated['password']);
        $account->save();

        ActivityLog::record('mail.account.created', 'Created mailbox '.$account->email, $account);

        return back()->with('success', 'Mailbox connected successfully.');
    }

    public function updateAccount(Request $request, AdminMailAccount $account): RedirectResponse
    {
        abort_unless($this->canManageAccounts(), 403);

        $validated = $this->validateAccount($request, $account);
        $test = $validated;
        $test['password'] = $validated['password'] ?: $account->plainPassword();
        $this->mailClient->testConnection($test);

        $account->fill($this->accountPayload($validated));
        if (! empty($validated['password'])) {
            $account->setPlainPassword($validated['password']);
        }
        $account->save();

        ActivityLog::record('mail.account.updated', 'Updated mailbox '.$account->email, $account);

        return back()->with('success', 'Mailbox updated.');
    }

    public function rules(Request $request): View
    {
        $account = $this->selectedAccount($request, $this->availableAccounts());
        abort_unless($account, 404);

        return view('admin.mail-inbox.rules', [
            'account' => $account,
            'accounts' => $this->availableAccounts(),
            'rules' => $account->rules()->with('assignee')->latest()->get(),
            'admins' => User::query()->where('is_admin', true)->orderBy('name')->get(),
        ]);
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $account = $this->accountById((int) $request->input('account_id'));
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'field' => ['required', Rule::in(['sender', 'subject', 'body'])],
            'value' => ['required', 'string', 'max:255'],
            'action' => ['required', Rule::in(['move', 'mark_read', 'assign'])],
            'target_folder' => ['nullable', Rule::in(['inbox', 'sent', 'drafts', 'trash', 'archive', 'spam'])],
            'assign_user_id' => ['nullable', 'exists:users,id'],
        ]);
        $validated['operator'] = 'contains';
        $validated['is_active'] = true;
        $account->rules()->create($validated);

        return back()->with('success', 'Rule saved.');
    }

    public function contacts(Request $request): View
    {
        $account = $this->selectedAccount($request, $this->availableAccounts());
        abort_unless($account, 404);

        return view('admin.mail-inbox.contacts', [
            'account' => $account,
            'accounts' => $this->availableAccounts(),
            'contacts' => $account->contacts()->orderBy('email')->paginate(50)->withQueryString(),
        ]);
    }

    public function compose(Request $request, ?AdminMailMessage $message = null): View
    {
        $accounts = $this->availableAccounts();
        $account = $message?->account ?: $this->selectedAccount($request, $accounts);
        abort_unless($account, 404);
        $this->authorizeMessageAccount($account);

        return view('admin.mail-inbox.compose', [
            'accounts' => $accounts,
            'account' => $account,
            'message' => $message,
            'mode' => $request->routeIs('admin.mail-inbox.forward') ? 'forward' : 'compose',
            'contacts' => $account->contacts()->orderBy('email')->limit(250)->get(),
        ]);
    }

    public function sync(Request $request): RedirectResponse
    {
        $account = $this->selectedAccount($request, $this->availableAccounts());
        if (! $account) {
            return back()->with('error', 'Connect a mailbox first.');
        }

        try {
            $count = $this->mailClient->syncAccount($account);
            $account->forceFill(['last_synced_at' => now(), 'last_sync_error' => null])->save();
            return back()->with('success', "Mailbox synced. {$count} new message(s) imported.");
        } catch (Throwable $exception) {
            $account->forceFill(['last_sync_error' => $exception->getMessage()])->save();
            return back()->with('error', 'Sync failed: '.$exception->getMessage());
        }
    }

    public function show(AdminMailMessage $message): View
    {
        $this->authorizeMessage($message);
        if ($message->direction === 'inbound' && ! $message->is_read) {
            $this->mailClient->setSeen($message, true);
        }

        return view('admin.mail-inbox.show', [
            'message' => $message->load(['replies.user', 'assignee', 'attachments', 'account']),
            'admins' => User::query()->where('is_admin', true)->orderBy('name')->get(),
            'accounts' => $this->availableAccounts(),
            'contacts' => $message->account?->contacts()->orderBy('email')->limit(250)->get() ?? collect(),
        ]);
    }

    public function update(Request $request, AdminMailMessage $message): RedirectResponse
    {
        $this->authorizeMessage($message);
        $action = $request->input('action', 'save');
        $sourceFolder = $message->folder ?: 'inbox';
        $movedToFolder = null;

        try {
            if ($action === 'mark_read') {
                $this->mailClient->setSeen($message, true);
            } elseif ($action === 'mark_unread') {
                $this->mailClient->setSeen($message, false);
            } elseif (in_array($action, ['archive', 'trash', 'spam'], true)) {
                $movedToFolder = $action === 'trash' ? 'trash' : $action;
                $this->mailClient->moveMessage($message, $movedToFolder);
            } elseif ($action === 'move') {
                $request->validate(['target_folder' => ['required', Rule::in(['inbox', 'sent', 'drafts', 'trash', 'archive', 'spam'])]]);
                $movedToFolder = $request->string('target_folder')->toString();
                $this->mailClient->moveMessage($message, $movedToFolder);
            } else {
                $validated = $request->validate([
                    'status' => ['required', 'in:open,pending,replied,closed,sent,failed,draft'],
                    'assigned_to_user_id' => ['nullable', 'exists:users,id'],
                ]);
                $message->update($validated);
            }
        } catch (Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        ActivityLog::record('mail.updated', 'Updated email '.$message->subject, $message);

        if ($movedToFolder === 'trash') {
            $returnFolder = in_array($sourceFolder, ['inbox', 'sent', 'drafts', 'archive', 'spam', 'failed', 'all'], true) ? $sourceFolder : 'inbox';

            return redirect()->route('admin.mail-inbox.index', [
                'account_id' => $message->account_id,
                'folder' => $returnFolder,
            ])->with('success', 'Email moved to Trash.');
        }

        return back()->with('success', 'Email updated.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:admin_mail_accounts,id'],
            'return_folder' => ['nullable', 'string', 'max:40'],
            'action' => ['required', Rule::in(['trash', 'archive', 'mark_read', 'mark_unread'])],
            'message_ids' => ['required', 'array', 'min:1'],
            'message_ids.*' => ['integer', 'exists:admin_mail_messages,id'],
        ]);

        $account = $this->accountById((int) $validated['account_id']);
        $messages = AdminMailMessage::query()
            ->where('account_id', $account->id)
            ->whereIn('id', $validated['message_ids'])
            ->get();

        if ($messages->isEmpty()) {
            return back()->with('error', 'Select at least one email first.');
        }

        $updated = 0;
        $failed = 0;

        foreach ($messages as $mailMessage) {
            try {
                if ($validated['action'] === 'mark_read') {
                    $this->mailClient->setSeen($mailMessage, true);
                } elseif ($validated['action'] === 'mark_unread') {
                    $this->mailClient->setSeen($mailMessage, false);
                } else {
                    $this->mailClient->moveMessage($mailMessage, $validated['action']);
                }
                $updated++;
            } catch (Throwable $exception) {
                report($exception);
                $failed++;
            }
        }

        ActivityLog::record('mail.bulk_updated', 'Bulk updated '.$updated.' email(s) with action '.$validated['action']);

        $returnFolder = $validated['return_folder'] ?? 'inbox';
        $returnFolder = in_array($returnFolder, ['inbox', 'sent', 'drafts', 'trash', 'archive', 'spam', 'failed', 'all'], true) ? $returnFolder : 'inbox';
        $message = $validated['action'] === 'trash'
            ? "{$updated} email(s) moved to Trash."
            : "{$updated} email(s) updated.";

        return redirect()->route('admin.mail-inbox.index', [
            'account_id' => $account->id,
            'folder' => $returnFolder,
        ])->with($failed > 0 ? 'error' : 'success', $failed > 0 ? $message." {$failed} failed." : $message);
    }

    public function send(Request $request): RedirectResponse
    {
        $account = $this->accountById((int) $request->input('account_id'));
        $validated = $request->validate([
            'account_id' => ['required', 'exists:admin_mail_accounts,id'],
            'to_email' => ['nullable', 'string', 'max:1000'],
            'cc_email' => ['nullable', 'string', 'max:1000'],
            'bcc_email' => ['nullable', 'string', 'max:1000'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body_html' => ['nullable', 'string', 'max:50000'],
            'body_text' => ['nullable', 'string', 'max:50000'],
            'parent_id' => ['nullable', 'exists:admin_mail_messages,id'],
            'attachments.*' => ['nullable', 'file', 'max:10240'],
        ]);

        if (! empty($validated['parent_id']) && empty($validated['subject'])) {
            $parent = AdminMailMessage::query()
                ->where('account_id', $account->id)
                ->find((int) $validated['parent_id']);

            if ($parent) {
                $parentSubject = trim((string) $parent->subject) ?: '(No subject)';
                $validated['subject'] = str_starts_with(strtolower($parentSubject), 're:') ? $parentSubject : 'Re: '.$parentSubject;
                $request->merge(['subject' => $validated['subject']]);
            }
        }

        if ($request->input('intent') !== 'draft') {            $request->validate(['to_email' => ['required', 'string'], 'subject' => ['required', 'string']]);
            if ($guardMessage = $this->deliveryGuardMessage($validated, $account)) {
                return back()->withInput()->with('error', $guardMessage);
            }
        }

        $storedFiles = $this->mailClient->storeUploadedFiles($request->file('attachments', []));
        $message = AdminMailMessage::query()->create([
            'account_id' => $account->id,
            'direction' => 'outbound',
            'mailbox' => $account->folderFor($request->input('intent') === 'draft' ? 'drafts' : 'sent'),
            'folder' => $request->input('intent') === 'draft' ? 'drafts' : 'sent',
            'from_email' => $account->email,
            'from_name' => $account->display_name,
            'to_email' => $validated['to_email'] ?? '',
            'cc_email' => $validated['cc_email'] ?? null,
            'bcc_email' => $validated['bcc_email'] ?? null,
            'subject' => $validated['subject'] ?: '(No subject)',
            'body_text' => $validated['body_text'] ?? strip_tags($validated['body_html'] ?? ''),
            'body_html' => $validated['body_html'] ?? null,
            'sent_at' => $request->input('intent') === 'draft' ? null : now(),
            'is_read' => true,
            'is_draft' => $request->input('intent') === 'draft',
            'status' => $request->input('intent') === 'draft' ? 'draft' : 'sent',
            'parent_id' => $validated['parent_id'] ?? null,
            'has_attachments' => count($storedFiles) > 0,
        ]);

        foreach ($storedFiles as $file) {
            AdminMailAttachment::query()->create(['admin_mail_message_id' => $message->id, 'disk' => 'local', ...$file]);
        }

        if ($request->input('intent') === 'draft') {
            $this->mailClient->saveDraft($account, $message);
            return redirect()->route('admin.mail-inbox.show', $message)->with('success', 'Draft saved.');
        }

        try {
            $this->mailClient->send($account, $validated, $storedFiles);
            $this->rememberSentContacts($account, $validated);
            ActivityLog::record('mail.sent', 'Sent email to '.$validated['to_email'], $message);
            return redirect()->route('admin.mail-inbox.show', $message)->with('success', 'Email sent and saved in Sent.');
        } catch (Throwable $exception) {
            $message->update(['status' => 'failed', 'folder' => 'failed']);
            return back()->withInput()->with('error', $this->friendlyMailError($exception->getMessage()));
        }
    }

    public function autosaveDraft(Request $request): JsonResponse
    {
        $account = $this->accountById((int) $request->input('account_id'));
        $validated = $request->validate([
            'draft_id' => ['nullable', 'exists:admin_mail_messages,id'],
            'to_email' => ['nullable', 'string', 'max:1000'],
            'cc_email' => ['nullable', 'string', 'max:1000'],
            'bcc_email' => ['nullable', 'string', 'max:1000'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body_html' => ['nullable', 'string', 'max:50000'],
            'body_text' => ['nullable', 'string', 'max:50000'],
        ]);

        $draft = ! empty($validated['draft_id'])
            ? AdminMailMessage::query()->where('account_id', $account->id)->where('is_draft', true)->find($validated['draft_id'])
            : null;

        $payload = [
            'account_id' => $account->id,
            'direction' => 'outbound',
            'mailbox' => $account->folderFor('drafts'),
            'folder' => 'drafts',
            'from_email' => $account->email,
            'from_name' => $account->display_name,
            'to_email' => $validated['to_email'] ?? '',
            'cc_email' => $validated['cc_email'] ?? null,
            'bcc_email' => $validated['bcc_email'] ?? null,
            'subject' => $validated['subject'] ?: '(No subject)',
            'body_text' => $validated['body_text'] ?? strip_tags($validated['body_html'] ?? ''),
            'body_html' => $validated['body_html'] ?? null,
            'is_read' => true,
            'is_draft' => true,
            'status' => 'draft',
        ];

        $draft ? $draft->update($payload) : $draft = AdminMailMessage::query()->create($payload);

        return response()->json(['ok' => true, 'draft_id' => $draft->id, 'saved_at' => now()->format('H:i')]);
    }

    public function reply(Request $request, AdminMailMessage $message): RedirectResponse
    {
        $this->authorizeMessage($message);
        $mode = $request->input('reply_mode', 'reply');
        $to = $mode === 'reply_all'
            ? collect(array_merge([$message->from_email], $this->mailClient->splitEmails($message->cc_email)))->reject(fn ($email) => strtolower($email) === strtolower($message->account->email))->implode(', ')
            : $message->from_email;

        $replySubject = trim((string) $message->subject) ?: '(No subject)';

        $request->merge([
            'account_id' => $message->account_id,
            'to_email' => $to,
            'subject' => str_starts_with(strtolower($replySubject), 're:') ? $replySubject : 'Re: '.$replySubject,
            'parent_id' => $message->id,
        ]);

        $this->prepareReplyPayload($request, $message);

        $response = $this->send($request);
        if ($response instanceof RedirectResponse && $response->getSession()?->get('success')) {
            AdminMailReply::query()->create([
                'admin_mail_message_id' => $message->id,
                'user_id' => auth()->id(),
                'to_email' => $to,
                'subject' => $request->input('subject'),
                'body_text' => $request->input('body_text') ?: strip_tags($request->input('body_html', '')),
                'sent_at' => now(),
                'delivery_status' => 'sent',
            ]);
            $message->update(['status' => 'replied', 'replied_at' => now()]);
        }
        return $response;
    }

    public function downloadAttachment(AdminMailAttachment $attachment): StreamedResponse
    {
        $message = $attachment->message;
        abort_unless($message && $this->canAccessAccount($message->account), 403);
        abort_unless(Storage::disk($attachment->disk)->exists($attachment->path), 404);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }

    private function liveMessagesQuery(Request $request, AdminMailAccount $account, string $folder)
    {
        return AdminMailMessage::query()
            ->select(['id', 'account_id', 'direction', 'mailbox', 'folder', 'from_email', 'from_name', 'to_email', 'subject', 'status', 'is_read', 'has_attachments', 'received_at', 'sent_at', 'created_at'])
            ->selectRaw('LEFT(COALESCE(body_text, body_html, ""), 700) as body_preview')
            ->with(['assignee:id,name', 'account:id,email,display_name'])
            ->where('account_id', $account->id)
            ->when($folder === 'failed', fn ($query) => $query->where('status', 'failed'))
            ->when($folder !== 'failed' && $folder !== 'all', fn ($query) => $query->where('folder', $folder))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->string('search'));
                $query->where(function ($query) use ($search) {
                    $query->where('subject', 'like', "%{$search}%")
                        ->orWhere('from_email', 'like', "%{$search}%")
                        ->orWhere('to_email', 'like', "%{$search}%")
                        ->orWhere('from_name', 'like', "%{$search}%")
                        ->orWhere('body_text', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByRaw('COALESCE(received_at, sent_at, created_at) DESC');
    }

    private function liveMessagePayload(AdminMailMessage $message): array
    {
        $contact = $message->direction === 'outbound'
            ? $message->to_email
            : ($message->from_name ?: $message->from_email);

        $body = preg_replace('/\s+/', ' ', trim(strip_tags((string) ($message->body_preview ?? ''))));
        $status = $message->status ?: 'open';

        return [
            'id' => $message->id,
            'url' => route('admin.mail-inbox.show', $message),
            'update_url' => route('admin.mail-inbox.update', $message),
            'folder' => $message->folder,
            'contact' => $contact ?: 'Unknown sender',
            'initials' => $this->mailInitials($contact),
            'subject' => $message->subject ?: '(No subject)',
            'preview' => Str::limit($body, 150),
            'date' => ($message->received_at ?: $message->sent_at ?: $message->created_at)?->format('M j, H:i'),
            'status' => $status,
            'status_class' => 'mail-status mail-status-'.(in_array($status, ['open', 'sent', 'failed', 'draft'], true) ? $status : 'default'),
            'unread' => ! $message->is_read && $message->folder === 'inbox',
            'has_attachments' => (bool) $message->has_attachments,
        ];
    }

    private function mailInitials(?string $value): string
    {
        $value = trim(strip_tags((string) $value));
        $words = preg_split('/\s+/', str_replace(['@', '.', '_', '-'], ' ', $value ?: 'Leivant Mail'));

        return strtoupper(substr($words[0] ?? 'L', 0, 1).substr($words[1] ?? ($words[0] ?? 'C'), 0, 1));
    }

    private function availableAccounts()
    {
        $query = AdminMailAccount::query()->where('is_active', true)->with('user')->orderBy('email');
        if (! $this->canManageAccounts()) {
            $query->where('user_id', auth()->id());
        }
        return $query->get();
    }

    private function selectedAccount(Request $request, $accounts): ?AdminMailAccount
    {
        if ($request->filled('account_id')) {
            return $accounts->firstWhere('id', (int) $request->input('account_id'));
        }
        return $accounts->first();
    }

    private function accountById(int $id): AdminMailAccount
    {
        $account = AdminMailAccount::query()->findOrFail($id);
        abort_unless($this->canAccessAccount($account), 403);
        return $account;
    }

    private function canAccessAccount(?AdminMailAccount $account): bool
    {
        if (! $account) {
            return false;
        }
        return $this->canManageAccounts() || $account->user_id === auth()->id();
    }

    private function authorizeMessage(AdminMailMessage $message): void
    {
        abort_unless($this->canAccessAccount($message->account), 403);
    }

    private function authorizeMessageAccount(AdminMailAccount $account): void
    {
        abort_unless($this->canAccessAccount($account), 403);
    }

    private function canManageAccounts(): bool
    {
        return auth()->user()?->role === 'super_admin';
    }

    private function folderCounts(?AdminMailAccount $account): array
    {
        $folders = ['inbox', 'sent', 'drafts', 'trash', 'archive', 'spam', 'failed', 'all'];
        if (! $account) {
            return array_fill_keys($folders, 0);
        }

        $row = AdminMailMessage::query()
            ->where('account_id', $account->id)
            ->selectRaw('COUNT(*) as all_count')
            ->selectRaw('COALESCE(SUM(folder = "inbox"), 0) as inbox_count')
            ->selectRaw('COALESCE(SUM(folder = "sent"), 0) as sent_count')
            ->selectRaw('COALESCE(SUM(folder = "drafts"), 0) as drafts_count')
            ->selectRaw('COALESCE(SUM(folder = "trash"), 0) as trash_count')
            ->selectRaw('COALESCE(SUM(folder = "archive"), 0) as archive_count')
            ->selectRaw('COALESCE(SUM(folder = "spam"), 0) as spam_count')
            ->selectRaw('COALESCE(SUM(status = "failed"), 0) as failed_count')
            ->selectRaw('COALESCE(SUM(folder = "inbox" AND is_read = 0), 0) as unread_count')
            ->first();

        return [
            'inbox' => (int) ($row->inbox_count ?? 0),
            'sent' => (int) ($row->sent_count ?? 0),
            'drafts' => (int) ($row->drafts_count ?? 0),
            'trash' => (int) ($row->trash_count ?? 0),
            'archive' => (int) ($row->archive_count ?? 0),
            'spam' => (int) ($row->spam_count ?? 0),
            'failed' => (int) ($row->failed_count ?? 0),
            'all' => (int) ($row->all_count ?? 0),
            'unread' => (int) ($row->unread_count ?? 0),
        ];
    }

    private function validateAccount(Request $request, ?AdminMailAccount $account = null): array
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'email' => ['required', 'email', 'max:180', Rule::unique('admin_mail_accounts', 'email')->ignore($account?->id)],
            'display_name' => ['nullable', 'string', 'max:180'],
            'imap_host' => ['required', 'string', 'max:180'],
            'imap_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'imap_encryption' => ['required', Rule::in(['ssl', 'none'])],
            'smtp_host' => ['required', 'string', 'max:180'],
            'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'smtp_encryption' => ['required', Rule::in(['ssl', 'none'])],
            'username' => ['required', 'string', 'max:180'],
            'password' => [$account ? 'nullable' : 'required', 'string', 'max:255'],
            'signature_html' => ['nullable', 'string', 'max:10000'],
            'is_active' => ['nullable', 'boolean'],
            'folders.inbox' => ['required', 'string', 'max:80'],
            'folders.sent' => ['required', 'string', 'max:80'],
            'folders.drafts' => ['required', 'string', 'max:80'],
            'folders.trash' => ['required', 'string', 'max:80'],
            'folders.archive' => ['required', 'string', 'max:80'],
            'folders.spam' => ['required', 'string', 'max:80'],
        ]);

        if (! str_ends_with(strtolower($validated['email']), '@leivantconstruction.com')) {
            abort(422, 'Only @leivantconstruction.com mailboxes are allowed.');
        }
        if (strtolower($validated['username']) !== strtolower($validated['email'])) {
            abort(422, 'Username must match the mailbox email address.');
        }

        return $validated;
    }

    private function accountPayload(array $validated): array
    {
        return [
            'user_id' => $validated['user_id'],
            'email' => strtolower($validated['email']),
            'display_name' => $validated['display_name'] ?? null,
            'imap_host' => $validated['imap_host'],
            'imap_port' => $validated['imap_port'],
            'imap_encryption' => $validated['imap_encryption'],
            'smtp_host' => $validated['smtp_host'],
            'smtp_port' => $validated['smtp_port'],
            'smtp_encryption' => $validated['smtp_encryption'],
            'username' => strtolower($validated['username']),
            'folders_json' => json_encode($validated['folders']),
            'signature_html' => $validated['signature_html'] ?? null,
            'is_active' => ! empty($validated['is_active']),
        ];
    }

    private function prepareReplyPayload(Request $request, AdminMailMessage $message): void
    {
        $bodyHtml = (string) $request->input('body_html', '');
        $bodyText = (string) $request->input('body_text', '');

        if (mb_strlen($this->compactMessageText($bodyText, $bodyHtml)) >= 40 || $this->hasQuotedReply($bodyText, $bodyHtml)) {
            return;
        }

        $quoteText = $this->readableMessageText($message);
        if ($quoteText === '') {
            return;
        }

        $dateSource = $message->received_at ?: $message->created_at;
        $date = $dateSource ? $dateSource->format('M j, Y H:i') : 'earlier';
        $from = trim($message->from_name ? $message->from_name.' <'.$message->from_email.'>' : (string) $message->from_email);
        $quoteText = Str::limit($quoteText, 900, '...');
        $quotedLines = implode("\n", array_map(fn ($line) => '> '.$line, preg_split('/\r\n|\r|\n/', $quoteText) ?: []));

        $request->merge([
            'body_text' => rtrim($bodyText)."\n\nOn {$date}, {$from} wrote:\n{$quotedLines}",
            'body_html' => rtrim($bodyHtml).'<br><br><div class="mail-reply-quote" style="border-left:3px solid #d4a017;margin-top:18px;padding-left:14px;color:#52525b;font-size:14px;"><p style="margin:0 0 8px;">On '.e($date).', '.e($from).' wrote:</p><blockquote style="margin:0;">'.nl2br(e($quoteText)).'</blockquote></div>',
        ]);
    }

    private function compactMessageText(?string $bodyText, ?string $bodyHtml = null): string
    {
        $source = trim((string) ($bodyText ?: $bodyHtml));

        return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($source), ENT_QUOTES, 'UTF-8')) ?? '');
    }

    private function readableMessageText(AdminMailMessage $message): string
    {
        $source = $message->body_text ?: preg_replace('/<br\s*\/?>/i', "\n", (string) $message->body_html);

        return trim(html_entity_decode(strip_tags((string) $source), ENT_QUOTES, 'UTF-8'));
    }

    private function hasQuotedReply(?string $bodyText, ?string $bodyHtml = null): bool
    {
        $body = strtolower((string) $bodyText.' '.(string) $bodyHtml);

        return str_contains($body, 'mail-reply-quote')
            || str_contains($body, '<blockquote')
            || str_contains($body, ' wrote:');
    }

    private function deliveryGuardMessage(array $validated, AdminMailAccount $account): ?string
    {
        $recipients = collect(['to_email', 'cc_email', 'bcc_email'])
            ->flatMap(fn ($field) => $this->mailClient->splitEmails($validated[$field] ?? ''))
            ->filter()
            ->values();

        if ($recipients->isEmpty()) {
            return null;
        }

        $systemRecipient = $recipients->first(fn ($email) => $this->isSystemMailRecipient($email));
        if ($systemRecipient) {
            return 'This is a delivery-system address, so replies are blocked. Open the original failed email, copy the real recipient address, then compose a new message to that person.';
        }

        $body = $this->compactMessageText($validated['body_text'] ?? '', $validated['body_html'] ?? '');
        $hasExternalRecipient = $recipients->contains(fn ($email) => ! str_ends_with(strtolower($email), '@leivantconstruction.com'));
        $isReply = ! empty($validated['parent_id']);

        if ($hasExternalRecipient && ! $isReply && mb_strlen($body) < 40) {
            return 'Please write a little more detail before sending external email. Very short messages are often rejected by the outgoing spam filter.';
        }

        if ($hasExternalRecipient && $isReply && mb_strlen($body) < 1) {
            return 'Write a short reply before sending.';
        }

        return null;
    }
    private function isSystemMailRecipient(string $email): bool
    {
        $localPart = strtolower(strtok(trim($email), '@') ?: $email);

        return in_array($localPart, ['mailer-daemon', 'postmaster'], true)
            || str_starts_with($localPart, 'no-reply')
            || str_starts_with($localPart, 'noreply');
    }

    private function friendlyMailError(string $message): string
    {
        if (str_contains(strtolower($message), 'high-probability spam')) {
            return 'Email was blocked by the outgoing spam filter. Do not reply to delivery reports, avoid random/very short text, and use a clear subject with a normal business message.';
        }

        return 'Email was not sent. '.$message;
    }

    private function rememberSentContacts(AdminMailAccount $account, array $validated): void
    {
        foreach (['to_email', 'cc_email', 'bcc_email'] as $field) {
            foreach ($this->mailClient->splitEmails($validated[$field] ?? '') as $email) {
                AdminMailContact::query()->updateOrCreate(
                    ['account_id' => $account->id, 'email' => strtolower($email)],
                    ['source' => 'sent', 'last_seen_at' => now()]
                );
            }
        }
    }
}