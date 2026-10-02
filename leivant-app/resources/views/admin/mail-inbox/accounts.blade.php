@extends('layouts.admin')

@section('title', 'Mailboxes | Leivant Admin')

@section('admin')
    <div class="space-y-6">
        <div class="admin-page-head"><div><p class="admin-eyebrow">Mailbox settings</p><h1>Mailboxes</h1><p>Connect each admin's Leivant mailbox using IMAP and SMTP.</p></div><a class="vant-button-outline" href="{{ route('admin.mail-inbox.index') }}">Back to Email</a></div>
        <div class="admin-card p-6">
            <h2 class="mb-4">Connected Mailboxes</h2>
            <div class="grid gap-3">
                @foreach ($accounts as $account)
                    <details class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
                        <summary class="cursor-pointer font-bold">{{ $account->email }} · {{ $account->user?->name ?: 'No owner' }} @if($account->last_sync_error)<span class="text-red-700"> · Sync issue</span>@endif</summary>
                        <form method="POST" action="{{ route('admin.mail-inbox.accounts.update', $account) }}" class="mt-4 grid gap-4">
                            @csrf @method('PATCH')
                            @include('admin.mail-inbox.partials.account-form', ['account' => $account])
                            <button class="vant-button" type="submit">Update Mailbox</button>
                        </form>
                    </details>
                @endforeach
            </div>
        </div>
        <div class="admin-card p-6">
            <h2 class="mb-4">Connect New Mailbox</h2>
            <form method="POST" action="{{ route('admin.mail-inbox.accounts.store') }}" class="grid gap-4">
                @csrf
                @include('admin.mail-inbox.partials.account-form', ['account' => null])
                <button class="vant-button" type="submit">Test and Connect</button>
            </form>
        </div>
    </div>
@endsection