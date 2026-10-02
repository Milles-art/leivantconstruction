@extends('layouts.admin')

@include('admin.mail-inbox.partials.premium-styles')

@section('title', 'Compose Email | Leivant Admin')

@section('admin')
    @php
        $isForward = $mode === 'forward' && $message;
        $subject = old('subject', $isForward ? 'Fwd: '.$message->subject : '');
        $forwardBody = $isForward ? '<br><br><hr><p><strong>Forwarded message</strong></p><p>From: '.e($message->from_name ?: $message->from_email).'<br>Subject: '.e($message->subject).'</p>'.(\App\Support\HtmlSanitizer::clean($message->body_html) ?: nl2br(e($message->body_text))) : '';
    @endphp

    <div class="mail-simple space-y-5">
        <section class="mail-topbar">
            <div>
                <h1>{{ $isForward ? 'Forward Email' : 'Compose Email' }}</h1>
                <p>From {{ $account->email }}</p>
            </div>
            <div class="mail-actions">
                <a class="vant-button-outline" href="{{ route('admin.mail-inbox.index', ['account_id' => $account->id]) }}">Back</a>
            </div>
        </section>

        <section class="mail-compose-layout">
            <form method="POST" action="{{ route('admin.mail-inbox.send') }}" enctype="multipart/form-data" class="mail-panel mail-composer" data-mail-compose>
                @csrf
                <input type="hidden" name="account_id" value="{{ $account->id }}">
                @if ($message)<input type="hidden" name="parent_id" value="{{ $message->id }}">@endif
                <input type="hidden" name="draft_id" data-draft-id>
                <input type="hidden" name="body_html" data-body-html>
                <input type="hidden" name="body_text" data-body-text>

                <label><span class="mb-2 block text-sm font-bold">To</span><input class="vant-input" name="to_email" list="mail-contacts" value="{{ old('to_email') }}" placeholder="client@example.com"></label>
                <div class="mail-compose-grid">
                    <label><span class="mb-2 block text-sm font-bold">Cc</span><input class="vant-input" name="cc_email" list="mail-contacts" value="{{ old('cc_email') }}"></label>
                    <label><span class="mb-2 block text-sm font-bold">Bcc</span><input class="vant-input" name="bcc_email" list="mail-contacts" value="{{ old('bcc_email') }}"></label>
                </div>
                <label><span class="mb-2 block text-sm font-bold">Subject</span><input class="vant-input" name="subject" value="{{ $subject }}" placeholder="Email subject"></label>

                <div>
                    <span class="mb-2 block text-sm font-bold">Message</span>
                    <div class="mail-editor-toolbar">
                        <button class="vant-button-outline mail-tool" type="button" data-command="bold">Bold</button>
                        <button class="vant-button-outline mail-tool" type="button" data-command="italic">Italic</button>
                        <button class="vant-button-outline mail-tool" type="button" data-command="insertUnorderedList">List</button>
                        <button class="vant-button-outline mail-tool" type="button" data-link>Link</button>
                    </div>
                    <div class="mail-editor" contenteditable="true" data-editor>{!! old('body_html', $forwardBody ?: $account->signature_html) !!}</div>
                    <p class="mt-2 text-xs font-bold text-zinc-500" data-draft-status>Draft autosave ready.</p>
                </div>

                <div class="mail-attach-zone">
                    <span class="mb-2 block text-sm font-bold">Attachments</span>
                    <input class="vant-input" type="file" name="attachments[]" multiple>
                </div>

                <datalist id="mail-contacts">@foreach ($contacts as $contact)<option value="{{ $contact->email }}">{{ $contact->name }}</option>@endforeach</datalist>
                <div class="flex flex-wrap gap-3">
                    <button class="vant-button" name="intent" value="send" type="submit">Send Email</button>
                    <button class="vant-button-outline" name="intent" value="draft" type="submit">Save Draft</button>
                    <a class="vant-button-outline" href="{{ route('admin.mail-inbox.index', ['account_id' => $account->id]) }}">Cancel</a>
                </div>
            </form>

            <aside class="mail-panel mail-action-panel">
                <h2>Composer</h2>
                <p class="mail-note">Autosave runs every 20 seconds while you write. Keep it simple: write, attach files if needed, then send.</p>
                <p class="mail-note mt-4"><strong>From:</strong><br>{{ $account->email }}</p>
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
    const draftId = form.querySelector('[data-draft-id]');
    const status = form.querySelector('[data-draft-status]');
    const syncFields = () => { html.value = editor.innerHTML; text.value = editor.innerText; };
    form.querySelectorAll('[data-command]').forEach(button => button.addEventListener('click', () => { document.execCommand(button.dataset.command, false, null); editor.focus(); syncFields(); }));
    const link = form.querySelector('[data-link]');
    if (link) link.addEventListener('click', () => { const url = prompt('URL'); if (url) document.execCommand('createLink', false, url); editor.focus(); syncFields(); });
    form.addEventListener('submit', syncFields);
    editor.addEventListener('input', syncFields);
    setInterval(() => {
        syncFields();
        if (!html.value.trim() && !form.subject.value.trim() && !form.to_email.value.trim()) return;
        const data = new FormData();
        ['account_id','to_email','cc_email','bcc_email','subject'].forEach(name => data.append(name, form.elements[name]?.value || ''));
        data.append('draft_id', draftId.value || '');
        data.append('body_html', html.value);
        data.append('body_text', text.value);
        fetch('{{ route('admin.mail-inbox.drafts.autosave') }}', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: data})
            .then(r => r.json()).then(json => { if (json.ok) { draftId.value = json.draft_id; status.textContent = 'Draft autosaved at ' + json.saved_at; } }).catch(() => {});
    }, 20000);
    syncFields();
})();
</script>
@endpush