@extends('layouts.admin')

@include('admin.mail-inbox.partials.premium-styles')

@section('title', 'Email | Leivant Admin')

@section('admin')
    @php
        $folderLabels = ['inbox' => 'Inbox', 'sent' => 'Sent', 'drafts' => 'Drafts', 'trash' => 'Trash', 'archive' => 'Archive', 'spam' => 'Junk', 'failed' => 'Failed', 'all' => 'All Mail'];
        $totalMessages = $folders['all'] ?? 0;
        $unreadMessages = $folders['unread'] ?? 0;
        $statusClass = fn ($status) => 'mail-status mail-status-'.(in_array($status, ['open', 'sent', 'failed', 'draft'], true) ? $status : 'default');
        $initials = function ($value) {
            $value = trim(strip_tags((string) $value));
            $words = preg_split('/\s+/', str_replace(['@', '.', '_', '-'], ' ', $value ?: 'Leivant Mail'));
            return strtoupper(substr($words[0] ?? 'L', 0, 1).substr($words[1] ?? ($words[0] ?? 'C'), 0, 1));
        };
        $liveQuery = $account ? array_filter([
            'account_id' => $account->id,
            'folder' => $folder,
            'status' => request('status'),
            'search' => request('search'),
        ], fn ($value) => $value !== null && $value !== '') : [];
    @endphp

    <div class="mail-simple space-y-5"
        @if ($account)
            data-mail-live
            data-live-url="{{ route('admin.mail-inbox.live', $liveQuery) }}"
            data-folder-label="{{ $folderLabels[$folder] ?? 'Mail' }}"
        @endif
    >
        <section class="mail-topbar">
            <div>
                <h1>Email</h1>
                <p>{{ $account?->email ?: 'Connect a mailbox to start using admin email.' }}</p>
            </div>
            <div class="mail-actions">
                @if ($account)
                    <a class="vant-button" href="{{ route('admin.mail-inbox.compose', ['account_id' => $account->id]) }}">Compose</a>
                    <form method="POST" action="{{ route('admin.mail-inbox.sync', ['account_id' => $account->id]) }}">
                        @csrf
                        <button class="vant-button-outline" type="submit">Sync</button>
                    </form>
                @endif
                @if ($canManageAccounts)
                    <a class="vant-button-outline" href="{{ route('admin.mail-inbox.accounts') }}">Mailboxes</a>
                @endif
            </div>
        </section>

        @if (! $account)
            <section class="mail-empty">
                <h2>No mailbox connected</h2>
                <p class="mail-note">A super admin needs to connect a Leivant mailbox before this email client can be used.</p>
                @if ($canManageAccounts)
                    <a class="vant-button mt-4" href="{{ route('admin.mail-inbox.accounts') }}">Connect Mailbox</a>
                @endif
            </section>
        @else
            <section class="mail-layout">
                <aside class="mail-panel mail-sidebar">
                    <form method="GET" action="{{ route('admin.mail-inbox.index') }}">
                        <input type="hidden" name="folder" value="{{ $folder }}">
                        @if ($accounts->count() > 1)
                            <select class="vant-input" name="account_id" onchange="this.form.submit()">
                                @foreach ($accounts as $mailAccount)
                                    <option value="{{ $mailAccount->id }}" @selected($account->id === $mailAccount->id)>{{ $mailAccount->email }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="mail-account">
                                <span>Mailbox</span>
                                <strong>{{ $account->email }}</strong>
                            </div>
                        @endif
                    </form>

                    <nav class="mail-folder-list" aria-label="Mail folders">
                        @foreach ($folderLabels as $key => $label)
                            <a class="mail-folder {{ $folder === $key ? 'is-active' : '' }}" href="{{ route('admin.mail-inbox.index', ['account_id' => $account->id, 'folder' => $key]) }}">
                                <span>{{ $label }}</span>
                                <span class="mail-count {{ $key === 'inbox' && $unreadMessages > 0 ? 'is-hot' : '' }}" data-folder-count="{{ $key }}">{{ $folders[$key] ?? 0 }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="mail-side-links">
                        <a href="{{ route('admin.mail-inbox.contacts', ['account_id' => $account->id]) }}">Contacts</a>
                        <a href="{{ route('admin.mail-inbox.rules', ['account_id' => $account->id]) }}">Rules</a>
                    </div>

                    <p class="mt-4 text-xs font-bold {{ $account->last_sync_error ? 'text-red-700' : 'text-zinc-500' }}" data-mail-sync-note>
                        @if ($account->last_sync_error)
                            Sync issue: {{ $account->last_sync_error }}
                        @else
                            Last sync: {{ $account->last_synced_at?->diffForHumans() ?: 'waiting' }}
                        @endif
                    </p>
                </aside>

                <main class="mail-panel mail-main">
                    <div class="mail-main-head">
                        <div>
                            <h2>{{ $folderLabels[$folder] ?? 'Mail' }}</h2>
                            <p><span data-mail-unread>{{ $unreadMessages }}</span> unread &middot; <span data-mail-total>{{ $totalMessages }}</span> total</p>
                        </div>
                        <div class="mail-live-indicator" data-mail-live-status>
                            <span class="mail-live-dot"></span>
                            <span>Live updates on</span>
                        </div>
                    </div>

                    <form id="mail-bulk-form" class="mail-bulk-toolbar" method="POST" action="{{ route('admin.mail-inbox.bulk') }}" data-mail-bulk-form>
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="account_id" value="{{ $account->id }}">
                        <input type="hidden" name="return_folder" value="{{ $folder }}">
                        <label class="mail-select-all"><input type="checkbox" data-mail-select-all> Select all</label>
                        <div class="mail-bulk-actions">
                            <button class="vant-button-outline" type="submit" name="action" value="mark_read">Mark read</button>
                            <button class="vant-button-outline" type="submit" name="action" value="mark_unread">Mark unread</button>
                            <button class="vant-button-danger" type="submit" name="action" value="trash">Move selected to Trash</button>
                        </div>
                    </form>

                    <form class="mail-filter" method="GET" action="{{ route('admin.mail-inbox.index') }}">
                        <input type="hidden" name="account_id" value="{{ $account->id }}">
                        <input type="hidden" name="folder" value="{{ $folder }}">
                        <input class="vant-input" name="search" value="{{ request('search') }}" placeholder="Search sender, recipient, subject, or message">
                        <select class="vant-input" name="status">
                            <option value="">All statuses</option>
                            @foreach (['open' => 'Open', 'pending' => 'Pending', 'replied' => 'Replied', 'closed' => 'Closed', 'sent' => 'Sent', 'failed' => 'Failed', 'draft' => 'Draft'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="vant-button" type="submit">Search</button>
                    </form>

                    <div class="mail-list" data-mail-list>
                        @forelse ($messages as $message)
                            @php
                                $contact = $message->direction === 'outbound' ? $message->to_email : ($message->from_name ?: $message->from_email);
                                $preview = \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', trim(strip_tags((string) ($message->body_preview ?? '')))), 150);
                                $date = ($message->received_at ?: $message->sent_at ?: $message->created_at)?->format('M j, H:i');
                            @endphp
                            <div class="mail-row-wrap">
                                <label class="mail-check" title="Select email"><input form="mail-bulk-form" type="checkbox" name="message_ids[]" value="{{ $message->id }}" data-mail-check aria-label="Select {{ $message->subject ?: 'email' }}"></label>
                                <a class="mail-row {{ ! $message->is_read && $message->folder === 'inbox' ? 'is-unread' : '' }}" href="{{ route('admin.mail-inbox.show', $message) }}">
                                    <span class="mail-avatar">{{ $initials($contact) }}</span>
                                <span class="mail-row-main">
                                    <span class="mail-row-top">
                                        <span class="mail-contact">{{ $contact }}</span>
                                        @if ($message->has_attachments)
                                            <span class="mail-status mail-status-default">File</span>
                                        @endif
                                    </span>
                                    <span class="mail-subject">{{ $message->subject }}</span>
                                    <span class="mail-preview">{{ $preview ?: 'No preview available.' }}</span>
                                </span>
                                    <span class="mail-meta">
                                        <span>{{ $date }}</span>
                                        <span class="{{ $statusClass($message->status) }}">{{ $message->status }}</span>
                                    </span>
                                </a>
                                @if ($message->folder !== 'trash')
                                    <form method="POST" action="{{ route('admin.mail-inbox.update', $message) }}" class="mail-row-trash" title="Move to Trash">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="trash">
                                        <button type="submit">Trash</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="mail-empty">
                                <h2>No emails in {{ $folderLabels[$folder] ?? $folder }}</h2>
                                <p class="mail-note">Use Sync to check the server, or compose a new message.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $messages->links() }}
                    </div>
                </main>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const root = document.querySelector('[data-mail-live]');
    if (!root) return;

    const liveUrl = root.dataset.liveUrl;
    const list = root.querySelector('[data-mail-list]');
    const status = root.querySelector('[data-mail-live-status]');
    const syncNote = root.querySelector('[data-mail-sync-note]');
    const unread = root.querySelector('[data-mail-unread]');
    const total = root.querySelector('[data-mail-total]');
    const folderLabel = root.dataset.folderLabel || 'Mail';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let busy = false;

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[char]));

    const setStatus = (text, failed = false) => {
        if (!status) return;
        status.classList.toggle('is-error', failed);
        status.querySelector('span:last-child').textContent = text;
    };

    const renderMessage = (message) => `
        <div class="mail-row-wrap">
            <label class="mail-check" title="Select email"><input form="mail-bulk-form" type="checkbox" name="message_ids[]" value="${escapeHtml(message.id)}" data-mail-check aria-label="Select ${escapeHtml(message.subject || 'email')}"></label>
            <a class="mail-row ${message.unread ? 'is-unread' : ''}" href="${escapeHtml(message.url)}">
                <span class="mail-avatar">${escapeHtml(message.initials)}</span>
                <span class="mail-row-main">
                    <span class="mail-row-top">
                        <span class="mail-contact">${escapeHtml(message.contact)}</span>
                        ${message.has_attachments ? '<span class="mail-status mail-status-default">File</span>' : ''}
                    </span>
                    <span class="mail-subject">${escapeHtml(message.subject)}</span>
                    <span class="mail-preview">${escapeHtml(message.preview || 'No preview available.')}</span>
                </span>
                <span class="mail-meta">
                    <span>${escapeHtml(message.date || '')}</span>
                    <span class="${escapeHtml(message.status_class)}">${escapeHtml(message.status)}</span>
                </span>
            </a>
            ${message.folder === 'trash' ? '' : `<form method="POST" action="${escapeHtml(message.update_url)}" class="mail-row-trash" title="Move to Trash"><input type="hidden" name="_token" value="${escapeHtml(csrf)}"><input type="hidden" name="_method" value="PATCH"><input type="hidden" name="action" value="trash"><button type="submit">Trash</button></form>`}
        </div>`;

    const renderEmpty = () => `
        <div class="mail-empty">
            <h2>No emails in ${escapeHtml(folderLabel)}</h2>
            <p class="mail-note">Use Sync to check the server, or compose a new message.</p>
        </div>`;

    const applyPayload = (payload) => {
        if (!payload.ok) throw new Error(payload.message || 'Live update failed.');

        Object.entries(payload.counts || {}).forEach(([folder, count]) => {
            root.querySelectorAll(`[data-folder-count="${folder}"]`).forEach((node) => {
                node.textContent = count;
                if (folder === 'inbox') {
                    node.classList.toggle('is-hot', Number(payload.unread || 0) > 0);
                }
            });
        });

        if (unread) unread.textContent = payload.unread ?? 0;
        if (total) total.textContent = payload.total ?? 0;
        if (list && !root.querySelector('[data-mail-check]:checked')) {
            list.innerHTML = payload.messages?.length ? payload.messages.map(renderMessage).join('') : renderEmpty();
        }

        if (syncNote) {
            syncNote.classList.toggle('text-red-700', Boolean(payload.last_sync_error));
            syncNote.classList.toggle('text-zinc-500', !payload.last_sync_error);
            syncNote.textContent = payload.last_sync_error
                ? `Sync issue: ${payload.last_sync_error}`
                : `Last sync: ${payload.last_sync || 'waiting'}`;
        }

        setStatus(payload.synced ? 'Updated just now' : 'Live updates on');
    };

    root.addEventListener('change', (event) => {
        if (!event.target.matches('[data-mail-select-all]')) return;
        root.querySelectorAll('[data-mail-check]').forEach((checkbox) => { checkbox.checked = event.target.checked; });
    });

    const bulkForm = root.querySelector('[data-mail-bulk-form]');
    bulkForm?.addEventListener('submit', (event) => {
        if (!root.querySelector('[data-mail-check]:checked')) {
            event.preventDefault();
            setStatus('Select at least one email', true);
        }
    });

    const poll = () => {
        if (busy || document.hidden || !liveUrl) return;
        busy = true;

        fetch(liveUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then((response) => {
                if (!response.ok) throw new Error('Live update failed.');
                return response.json();
            })
            .then(applyPayload)
            .catch(() => setStatus('Live update paused', true))
            .finally(() => { busy = false; });
    };

    window.setTimeout(poll, 30000);
    window.setInterval(poll, 60000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) poll();
    });
})();
</script>
@endpush