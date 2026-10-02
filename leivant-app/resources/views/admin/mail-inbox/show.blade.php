@extends('layouts.admin')

@include('admin.mail-inbox.partials.premium-styles')

@section('title', 'Email Conversation | Leivant Admin')

@section('admin')
    @php
        $contact = $message->direction === 'outbound' ? $message->to_email : ($message->from_name ?: $message->from_email);
        $initials = function ($value) {
            $value = trim(strip_tags((string) $value));
            $words = preg_split('/\s+/', str_replace(['@', '.', '_', '-'], ' ', $value ?: 'Leivant Mail'));
            return strtoupper(substr($words[0] ?? 'L', 0, 1).substr($words[1] ?? ($words[0] ?? 'C'), 0, 1));
        };
        $statusClass = 'mail-status mail-status-'.(in_array($message->status, ['open', 'sent', 'failed', 'draft'], true) ? $message->status : 'default');
        $isSystemNotice = preg_match('/mailer-daemon|postmaster|undelivered mail returned/i', (string) $message->from_email.' '.(string) $message->from_name.' '.(string) $message->subject);
    @endphp

    <div class="mail-simple space-y-5">
        <section class="mail-topbar">
            <div>
                <h1>Email</h1>
                <p>{{ $message->account?->email }}</p>
            </div>
            <div class="mail-actions">
                <a class="vant-button-outline" href="{{ route('admin.mail-inbox.forward', $message) }}">Forward</a>
                <a class="vant-button-outline" href="{{ route('admin.mail-inbox.index', ['account_id' => $message->account_id, 'folder' => $message->folder]) }}">Back</a>
            </div>
        </section>

        <section class="mail-reader-layout">
            <main class="mail-panel mail-reader">
                <div class="mail-reader-head">
                    <span class="mail-avatar">{{ $initials($contact) }}</span>
                    <div class="min-w-0">
                        <h2>{{ $message->subject }}</h2>
                        <div class="mail-pills">
                            <span class="admin-pill">{{ $message->direction === 'outbound' ? 'To' : 'From' }} {{ $contact }}</span>
                            <span class="{{ $statusClass }}">{{ $message->status }}</span>
                            <span class="admin-pill">{{ ($message->received_at ?: $message->sent_at ?: $message->created_at)?->format('M j, Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                @if ($message->cc_email)
                    <p class="mt-4 text-sm font-bold text-zinc-500">Cc: {{ $message->cc_email }}</p>
                @endif

                <div class="mail-body">
                    {!! \App\Support\HtmlSanitizer::clean($message->body_html) ?: nl2br(e($message->body_text)) !!}
                </div>

                @if ($message->attachments->isNotEmpty())
                    <div class="mail-attach-zone">
                        <p class="mb-3 text-sm font-black uppercase tracking-wide text-zinc-700">Attachments</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($message->attachments as $attachment)
                                <a class="admin-pill" href="{{ route('admin.mail-inbox.attachments.download', $attachment) }}">{{ $attachment->original_name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($message->direction === 'inbound' && $isSystemNotice)
                    <div class="mail-reply-box">
                        <h2 class="mb-3">Delivery notice</h2>
                        <p class="mail-note">This message came from the mail delivery system. Replies to delivery reports are blocked because mail servers treat them as spam. Compose a new email to the real recipient instead.</p>
                    </div>
                @endif

                @if ($message->direction === 'inbound' && ! $isSystemNotice)
                    <div class="mail-reply-box">
                        <h2 class="mb-4">Reply</h2>
                        <form method="POST" action="{{ route('admin.mail-inbox.reply', $message) }}" enctype="multipart/form-data" class="space-y-4" data-mail-compose>
                            @csrf
                            <input type="hidden" name="body_html" data-body-html>
                            <input type="hidden" name="body_text" data-body-text>
                            <div class="flex flex-wrap gap-3">
                                <label class="admin-pill"><input type="radio" name="reply_mode" value="reply" checked> Reply</label>
                                <label class="admin-pill"><input type="radio" name="reply_mode" value="reply_all"> Reply all</label>
                            </div>
                            <div class="mail-compose-grid">
                                <input class="vant-input" name="cc_email" list="mail-contacts" placeholder="Cc">
                                <input class="vant-input" name="bcc_email" list="mail-contacts" placeholder="Bcc">
                            </div>
                            <div class="mail-editor" contenteditable="true" data-editor>{!! \App\Support\HtmlSanitizer::clean($message->account?->signature_html) !!}</div>
                            <div class="mail-attach-zone">
                                <input class="vant-input" type="file" name="attachments[]" multiple>
                            </div>
                            <datalist id="mail-contacts">@foreach ($contacts as $contactItem)<option value="{{ $contactItem->email }}">{{ $contactItem->name }}</option>@endforeach</datalist>
                            <button class="vant-button" type="submit">Send Reply</button>
                        </form>
                    </div>
                @endif
            </main>

            <aside class="mail-panel mail-action-panel">
                <h2>Actions</h2>
                <div class="mail-action-grid">
                    <form method="POST" action="{{ route('admin.mail-inbox.update', $message) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="{{ $message->is_read ? 'mark_unread' : 'mark_read' }}"><button class="vant-button-outline" type="submit">{{ $message->is_read ? 'Mark Unread' : 'Mark Read' }}</button></form>
                    <form method="POST" action="{{ route('admin.mail-inbox.update', $message) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="archive"><button class="vant-button-outline" type="submit">Archive</button></form>
                    <form method="POST" action="{{ route('admin.mail-inbox.update', $message) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="trash"><button class="vant-button-outline" type="submit">Move to Trash</button></form>
                    <form method="POST" action="{{ route('admin.mail-inbox.update', $message) }}" class="space-y-3">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="move">
                        <select class="vant-input" name="target_folder">
                            @foreach (['inbox'=>'Inbox','sent'=>'Sent','drafts'=>'Drafts','trash'=>'Trash','archive'=>'Archive','spam'=>'Junk'] as $value=>$label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="vant-button-outline" type="submit">Move Folder</button>
                    </form>
                    <form method="POST" action="{{ route('admin.mail-inbox.update', $message) }}" class="space-y-3">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="save">
                        <select class="vant-input" name="status">
                            @foreach (['open'=>'Open','pending'=>'Pending','replied'=>'Replied','closed'=>'Closed','sent'=>'Sent','failed'=>'Failed','draft'=>'Draft'] as $value=>$label)
                                <option value="{{ $value }}" @selected($message->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select class="vant-input" name="assigned_to_user_id">
                            <option value="">Unassigned</option>
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}" @selected($message->assigned_to_user_id === $admin->id)>{{ $admin->name }}</option>
                            @endforeach
                        </select>
                        <button class="vant-button" type="submit">Save</button>
                    </form>
                </div>
            </aside>
        </section>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.querySelector('[data-mail-compose]');
    if (!form) return;
    const editor = form.querySelector('[data-editor]');
    const html = form.querySelector('[data-body-html]');
    const text = form.querySelector('[data-body-text]');
    const sync = () => { html.value = editor.innerHTML; text.value = editor.innerText; };
    editor.addEventListener('input', sync);
    form.addEventListener('submit', sync);
    sync();
})();
</script>
@endpush