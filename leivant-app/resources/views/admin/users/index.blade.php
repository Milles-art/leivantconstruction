@extends('layouts.admin')

@section('title', 'Users | Leivant Admin')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-eyebrow">Access Control</p>
            <h1 class="mt-2">Users & Admins</h1>
            <p class="mt-2 max-w-2xl text-sm">Create admin users, connect provider accounts, and manage access.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="vant-button">New User</a>
    </div>

    <form method="GET" class="admin-filter-bar mt-6 md:grid-cols-[1fr_auto_auto]">
        <input class="vant-input min-h-12" name="search" value="{{ request('search') }}" placeholder="Search name or email">
        <select class="vant-input min-h-12" name="role">
            <option value="">All roles</option>
            @foreach (['super_admin' => 'Super Admin', 'manager' => 'Manager', 'sales' => 'Sales', 'content' => 'Content', 'provider' => 'Provider'] as $value => $label)
                <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="vant-button" type="submit">Filter</button>
    </form>

    <div class="admin-card mt-7 overflow-hidden">
        <table class="w-full min-w-[840px] text-left text-sm">
            <thead><tr><th class="px-4 py-3">User</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Provider</th><th class="px-4 py-3">Created</th><th class="px-4 py-3">Actions</th></tr></thead>
            <tbody class="divide-y divide-zinc-100">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-4"><p class="font-bold text-zinc-950">{{ $user->name }}</p><p class="text-xs text-zinc-500">{{ $user->email }}</p></td>
                        <td class="px-4 py-4"><span class="admin-badge bg-vant-gold/10 text-vant-gold">{{ str($user->role ?? 'provider')->headline() }}</span></td>
                        <td class="px-4 py-4"><span class="admin-pill">{{ $user->is_admin ? 'Admin' : str($user->account_type)->title() }}</span></td>
                        <td class="px-4 py-4 text-zinc-600">{{ $user->provider?->name ?? 'None' }}</td>
                        <td class="px-4 py-4 text-zinc-600">{{ $user->created_at?->format('d M Y') }}</td>
                        <td class="px-4 py-4">
                            <div class="flex gap-3">
                                <a class="font-bold text-vant-gold" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                <span class="text-xs font-bold uppercase tracking-wide text-zinc-500">Protected</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-zinc-100 p-4">{{ $users->links() }}</div>
    </div>
@endsection
