@extends('layouts.admin')

@section('title', 'Mail Contacts | Leivant Admin')

@section('admin')
    <div class="space-y-6">
        <div class="admin-page-head"><div><p class="admin-eyebrow">{{ $account->email }}</p><h1>Contacts</h1><p>Address book collected from sent and received messages.</p></div><a class="vant-button-outline" href="{{ route('admin.mail-inbox.index', ['account_id' => $account->id]) }}">Back to Email</a></div>
        <div class="admin-table-wrap overflow-x-auto"><table class="min-w-full divide-y divide-zinc-200 text-sm"><thead><tr><th class="px-4 py-3 text-left">Email</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Source</th><th class="px-4 py-3 text-left">Last Seen</th></tr></thead><tbody class="divide-y divide-zinc-200">@foreach($contacts as $contact)<tr><td class="px-4 py-3 font-bold">{{ $contact->email }}</td><td class="px-4 py-3">{{ $contact->name }}</td><td class="px-4 py-3">{{ $contact->source }}</td><td class="px-4 py-3">{{ $contact->last_seen_at?->format('M j, Y') }}</td></tr>@endforeach</tbody></table></div>
        {{ $contacts->links() }}
    </div>
@endsection