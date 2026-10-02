@extends('layouts.admin')

@section('title', 'Inquiries Admin | Leivant')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-eyebrow">Inquiries</p>
            <h1 class="mt-2">Service Request Inbox</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6">Structured customer requests from service pages and contact forms, ready for follow-up by the Leivant Construction Desk.</p>
        </div>
    </div>

    <form method="GET" class="admin-filter-bar mt-6 md:grid-cols-[1fr_auto_auto_auto_auto]">
        <input class="vant-input min-h-12" name="search" value="{{ request('search') }}" placeholder="Search name, phone, subject, or message">
        <select class="vant-input min-h-12" name="status">
            <option value="">All status</option>
            @foreach (['new', 'in_progress', 'responded', 'closed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->headline() }}</option>
            @endforeach
        </select>
        <select class="vant-input min-h-12" name="priority">
            <option value="">All priority</option>
            @foreach (['low', 'normal', 'high', 'urgent'] as $priority)
                <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ str($priority)->headline() }}</option>
            @endforeach
        </select>
        <select class="vant-input min-h-12" name="assigned_to">
            <option value="">Any owner</option>
            @foreach ($admins as $admin)
                <option value="{{ $admin->id }}" @selected(request('assigned_to') == $admin->id)>{{ $admin->name }}</option>
            @endforeach
        </select>
        <button class="vant-button" type="submit">Filter</button>
    </form>

    <form method="POST" action="{{ route('admin.inquiries.bulk') }}" class="mt-7 grid gap-4">
        @csrf
        <div class="admin-filter-bar md:grid-cols-[auto_auto_1fr_auto]">
            <select class="vant-input min-h-12" name="action" required>
                <option value="">Bulk action</option>
                <option value="in_progress">Mark in progress</option>
                <option value="responded">Mark responded</option>
                <option value="closed">Close selected</option>
                <option value="high_priority">Set high priority</option>
                <option value="normal_priority">Set normal priority</option>
            </select>
            <button class="vant-button-outline" type="submit">Apply</button>
            <span></span>
            <a href="{{ route('admin.inquiries.index') }}" class="self-center text-xs font-extrabold uppercase tracking-wide text-vant-gold">Reset filters</a>
        </div>
        @forelse ($inquiries as $inquiry)
            <div class="admin-card p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="checkbox" name="inquiries[]" value="{{ $inquiry->id }}">
                            <span class="admin-badge bg-vant-gold text-white">{{ $inquiry->service?->name ?? 'General' }}</span>
                            <span class="admin-badge {{ in_array($inquiry->priority, ['high', 'urgent'], true) ? 'bg-red-100 text-red-700' : 'bg-zinc-100 text-zinc-600' }}">{{ $inquiry->priority ?? 'normal' }}</span>
                            @if ($inquiry->region)
                                <span class="admin-badge bg-zinc-100 text-zinc-600">{{ $inquiry->region }}</span>
                            @endif
                        </div>
                        <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="mt-3 block font-heading text-2xl font-extrabold text-zinc-950 hover:text-vant-gold">{{ $inquiry->subject }}</a>
                        <p class="mt-1 text-sm text-zinc-600">{{ $inquiry->name }} | {{ $inquiry->phone }}{{ $inquiry->company ? ' | '.$inquiry->company : '' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="admin-badge bg-zinc-100 text-zinc-700">{{ str($inquiry->status)->replace('_', ' ')->title() }}</span>
                        <span class="admin-badge bg-vant-gold/10 text-vant-gold">{{ $inquiry->assignedUser?->name ?? 'Unassigned' }}</span>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 md:grid-cols-4">
                    <div class="rounded-lg border border-zinc-200 bg-white p-3">
                        <p class="text-xs font-bold uppercase text-zinc-500">Project Type</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-950">{{ $inquiry->project_type ?? 'Not specified' }}</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-3">
                        <p class="text-xs font-bold uppercase text-zinc-500">Site</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-950">{{ $inquiry->site_location ?? 'Not specified' }}</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-3">
                        <p class="text-xs font-bold uppercase text-zinc-500">Budget</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-950">{{ $inquiry->budget_range ?? 'Not specified' }}</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-white p-3">
                        <p class="text-xs font-bold uppercase text-zinc-500">Follow Up</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-950">{{ $inquiry->follow_up_at?->format('d M Y H:i') ?? 'Not scheduled' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="admin-card p-8 text-center">
                <h2>No inquiries yet.</h2>
                <p class="mt-2">Service requests and contact messages will appear here.</p>
            </div>
        @endforelse

        {{ $inquiries->links() }}
    </form>
@endsection

