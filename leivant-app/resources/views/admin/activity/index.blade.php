@extends('layouts.admin')

@section('title', 'Activity Log | Leivant Admin')

@section('admin')
    <p class="admin-eyebrow">Audit Trail</p>
    <h1 class="mt-2">Activity Log</h1>
    <p class="mt-2 max-w-2xl text-sm">Track admin changes across users, taxonomy, products, providers, services, projects, and settings.</p>
    <div class="admin-card mt-7 overflow-hidden">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead><tr><th class="px-4 py-3">Action</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">User</th><th class="px-4 py-3">IP</th><th class="px-4 py-3">Time</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-4 py-4"><span class="admin-pill">{{ $log->action }}</span></td>
                        <td class="px-4 py-4 font-semibold text-zinc-950">{{ $log->description }}</td>
                        <td class="px-4 py-4 text-zinc-600">{{ $log->user?->email ?? 'System' }}</td>
                        <td class="px-4 py-4 text-zinc-600">{{ $log->ip_address ?? 'n/a' }}</td>
                        <td class="px-4 py-4 text-zinc-600">{{ $log->created_at?->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-zinc-500">No activity recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-zinc-100 p-4">{{ $logs->links() }}</div>
    </div>
@endsection
