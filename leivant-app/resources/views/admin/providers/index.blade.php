@extends('layouts.admin')

@section('title', 'Providers Admin | Leivant')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-eyebrow">Providers</p>
            <h1 class="mt-2">Directory Providers</h1>
            <p class="mt-2 max-w-2xl text-sm">Review, approve, and manage businesses shown in the national discovery directory.</p>
        </div>
        <a href="{{ route('admin.providers.create') }}" class="vant-button">New Provider</a>
    </div>

    <form method="GET" class="admin-filter-bar mt-6 md:grid-cols-[1fr_auto_auto_auto_auto]">
        <input class="vant-input min-h-12" name="search" value="{{ request('search') }}" placeholder="Search provider, phone, email, or location">
        <select class="vant-input min-h-12" name="category">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <select class="vant-input min-h-12" name="region_id">
            <option value="">All regions</option>
            @foreach ($regions as $region)
                <option value="{{ $region->id }}" @selected(request('region_id') == $region->id)>{{ $region->name }}</option>
            @endforeach
        </select>
        <select class="vant-input min-h-12" name="status">
            <option value="">All status</option>
            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button class="vant-button" type="submit">Filter</button>
    </form>

    <form method="POST" action="{{ route('admin.providers.bulk') }}" class="admin-table-wrap mt-7">
        @csrf
        <div class="flex flex-col gap-2 border-b border-zinc-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2>Provider Registry</h2>
                <p class="mt-1 text-sm text-zinc-500">Approved and pending construction service listings.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select class="vant-input min-h-10" name="action" required>
                    <option value="">Bulk action</option>
                    <option value="approve">Approve</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="verify">Verify</option>
                    <option value="unverify">Unverify</option>
                </select>
                <button class="vant-button-outline py-2" type="submit">Apply</button>
                <span class="admin-pill">{{ $providers->total() }} providers</span>
            </div>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead class="text-xs uppercase tracking-wide text-zinc-200">
                <tr>
                    <th class="px-4 py-3"><input type="checkbox" onclick="document.querySelectorAll('[data-provider-check]').forEach((box) => box.checked = this.checked)"></th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Region</th>
                    <th class="px-4 py-3">Rating</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($providers as $provider)
                    <tr>
                        <td class="px-4 py-4"><input data-provider-check type="checkbox" name="providers[]" value="{{ $provider->id }}"></td>
                        <td class="px-4 py-4 font-bold text-zinc-950">{{ $provider->name }}</td>
                        <td class="px-4 py-4 text-zinc-700">{{ $provider->category }}</td>
                        <td class="px-4 py-4 text-zinc-700">{{ $provider->region?->name }}</td>
                        <td class="px-4 py-4 text-vant-gold">{{ $provider->rating }}</td>
                        <td class="px-4 py-4">
                            <span class="admin-badge {{ $provider->is_active && $provider->is_verified ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $provider->is_active && $provider->is_verified ? 'Approved' : 'Pending Review' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex gap-3">
                                <a href="{{ route('admin.providers.edit', $provider) }}" class="font-bold text-vant-gold hover:text-vant-orange">Edit</a>
                                @if ($provider->is_active)
                                    <form method="POST" action="{{ route('admin.providers.deactivate', $provider) }}" onsubmit="return confirm('Hide this provider from public discovery?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="font-bold text-vant-orange" type="submit">Deactivate</button>
                                    </form>
                                @else
                                    <span class="text-xs font-bold uppercase tracking-wide text-zinc-500">Inactive</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-zinc-500">No providers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="border-t border-zinc-100 p-4">{{ $providers->links() }}</div>
    </form>
@endsection

