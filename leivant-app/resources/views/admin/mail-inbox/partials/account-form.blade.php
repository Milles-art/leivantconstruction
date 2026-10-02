@php($folderValues = $account?->folders() ?? $folders)
<div class="grid gap-4 md:grid-cols-2">
    <label><span class="mb-2 block text-sm font-bold">Owner admin</span><select class="vant-input" name="user_id" required>@foreach ($admins as $admin)<option value="{{ $admin->id }}" @selected(old('user_id', $account->user_id ?? null) == $admin->id)>{{ $admin->name }} - {{ $admin->email }}</option>@endforeach</select></label>
    <label><span class="mb-2 block text-sm font-bold">Mailbox email</span><input class="vant-input" type="email" name="email" value="{{ old('email', $account->email ?? '') }}" placeholder="name@leivantconstruction.com" required></label>
    <label><span class="mb-2 block text-sm font-bold">Display name</span><input class="vant-input" name="display_name" value="{{ old('display_name', $account->display_name ?? 'Leivant Construction') }}"></label>
    <label><span class="mb-2 block text-sm font-bold">Username</span><input class="vant-input" name="username" value="{{ old('username', $account->username ?? '') }}" placeholder="name@leivantconstruction.com" required></label>
    <label><span class="mb-2 block text-sm font-bold">Password {{ $account ? '(leave blank to keep)' : '' }}</span><input class="vant-input" type="password" name="password" @required(! $account)></label>
    <label class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm font-bold"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account->is_active ?? true))> Active</label>
</div>
<div class="grid gap-4 md:grid-cols-4">
    <label><span class="mb-2 block text-sm font-bold">IMAP host</span><input class="vant-input" name="imap_host" value="{{ old('imap_host', $account->imap_host ?? 'mail.leivantconstruction.com') }}" required></label>
    <label><span class="mb-2 block text-sm font-bold">IMAP port</span><input class="vant-input" type="number" name="imap_port" value="{{ old('imap_port', $account->imap_port ?? 993) }}" required></label>
    <label><span class="mb-2 block text-sm font-bold">SMTP host</span><input class="vant-input" name="smtp_host" value="{{ old('smtp_host', $account->smtp_host ?? 'mail.leivantconstruction.com') }}" required></label>
    <label><span class="mb-2 block text-sm font-bold">SMTP port</span><input class="vant-input" type="number" name="smtp_port" value="{{ old('smtp_port', $account->smtp_port ?? 465) }}" required></label>
    <input type="hidden" name="imap_encryption" value="ssl"><input type="hidden" name="smtp_encryption" value="ssl">
</div>
<div class="grid gap-4 md:grid-cols-6">
    @foreach (['inbox'=>'Inbox','sent'=>'Sent','drafts'=>'Drafts','trash'=>'Trash','archive'=>'Archive','spam'=>'Junk'] as $key => $label)
        <label><span class="mb-2 block text-sm font-bold">{{ $label }} folder</span><input class="vant-input" name="folders[{{ $key }}]" value="{{ old('folders.'.$key, $folderValues[$key] ?? $folders[$key]) }}" required></label>
    @endforeach
</div>
<label><span class="mb-2 block text-sm font-bold">Signature HTML</span><textarea class="vant-input" name="signature_html">{{ old('signature_html', $account->signature_html ?? '') }}</textarea></label>