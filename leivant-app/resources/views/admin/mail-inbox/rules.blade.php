@extends('layouts.admin')

@section('title', 'Mail Rules | Leivant Admin')

@section('admin')
    <div class="space-y-6">
        <div class="admin-page-head"><div><p class="admin-eyebrow">{{ $account->email }}</p><h1>Rules</h1><p>Simple mailbox filters for new synced messages.</p></div><a class="vant-button-outline" href="{{ route('admin.mail-inbox.index', ['account_id' => $account->id]) }}">Back to Email</a></div>
        <form method="POST" action="{{ route('admin.mail-inbox.rules.store') }}" class="admin-card grid gap-4 p-6">
            @csrf
            <input type="hidden" name="account_id" value="{{ $account->id }}">
            <div class="grid gap-4 md:grid-cols-4"><input class="vant-input" name="name" placeholder="Rule name" required><select class="vant-input" name="field"><option value="sender">Sender</option><option value="subject">Subject</option><option value="body">Body</option></select><input class="vant-input" name="value" placeholder="Contains" required><select class="vant-input" name="action"><option value="move">Move folder</option><option value="mark_read">Mark read</option><option value="assign">Assign admin</option></select></div>
            <div class="grid gap-4 md:grid-cols-2"><select class="vant-input" name="target_folder"><option value="">Target folder</option><option value="archive">Archive</option><option value="trash">Trash</option><option value="spam">Junk</option></select><select class="vant-input" name="assign_user_id"><option value="">Assign to</option>@foreach($admins as $admin)<option value="{{ $admin->id }}">{{ $admin->name }}</option>@endforeach</select></div>
            <button class="vant-button" type="submit">Create Rule</button>
        </form>
        <div class="admin-card p-6"><div class="grid gap-3">@foreach($rules as $rule)<div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4"><strong>{{ $rule->name }}</strong><p class="text-sm">{{ $rule->field }} contains "{{ $rule->value }}" -> {{ $rule->action }} {{ $rule->target_folder ?: $rule->assignee?->name }}</p></div>@endforeach</div></div>
    </div>
@endsection