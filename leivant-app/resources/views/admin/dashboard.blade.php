@extends('layouts.admin')

@section('title', 'Admin Dashboard | Leivant')

@section('admin')
    @php
        $canAdmin = fn (string $permission): bool => (bool) auth()->user()?->hasAdminPermission($permission);

        $approvalQueue = $stats['pending_providers'] + $stats['reviews_pending'];
        $approvalRoute = $stats['pending_providers']
            ? route('admin.providers.index', ['status' => 'pending'])
            : route('admin.reviews.index', ['status' => 'pending']);

        $ownerMetrics = [
            ['New requests', $stats['new_inquiries'], route('admin.inquiries.index', ['status' => 'new']), 'pipeline.manage', 'Open client work'],
            ['Follow-ups due', $stats['due_followups'], route('admin.inquiries.index', ['due' => 1]), 'pipeline.manage', 'Call or reply next'],
            ['Approval queue', $approvalQueue, $approvalRoute, $stats['pending_providers'] ? 'providers.manage' : 'reviews.manage', 'Providers and reviews'],
        ];
    @endphp

    <section class="admin-page-head">
        <div>
            <p class="admin-eyebrow">Owner Dashboard</p>
            <h1>Today's Admin Focus</h1>
            <p>New client requests, due follow-ups, and pending approvals only. Everything else stays in its own module.</p>
        </div>
        @if ($canAdmin('pipeline.manage'))
            <a href="{{ route('admin.inquiries.index') }}" class="vant-button">Open Inbox</a>
        @endif
    </section>

    <section class="admin-metric-grid mt-5">
        @foreach ($ownerMetrics as [$label, $value, $url, $permission, $caption])
            @continue(! $canAdmin($permission))
            <a href="{{ $url }}" class="admin-metric-card">
                <span>{{ $label }}</span>
                <strong>{{ number_format($value) }}</strong>
                <small>{{ $value ? $caption : 'Clear' }}</small>
            </a>
        @endforeach
    </section>

    <div class="admin-dashboard-grid mt-6">
        @if ($canAdmin('pipeline.manage'))
            <section class="admin-card admin-card-plain">
                <div class="admin-card-header">
                    <div>
                        <p class="admin-eyebrow">Client Pipeline</p>
                        <h2>Latest requests.</h2>
                    </div>
                    <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-extrabold uppercase tracking-wide text-vant-gold">View All</a>
                </div>
                <div class="divide-y divide-zinc-100">
                    @forelse ($recentInquiries->take(5) as $inquiry)
                        <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="admin-list-link">
                            <span>
                                <span class="block font-extrabold text-zinc-950">{{ $inquiry->subject }}</span>
                                <span class="mt-1 block text-sm text-zinc-600">{{ $inquiry->name }} | {{ $inquiry->phone }}</span>
                            </span>
                            <span class="admin-badge {{ in_array($inquiry->priority, ['high', 'urgent'], true) ? 'bg-red-100 text-red-700' : 'bg-zinc-100 text-zinc-700' }}">
                                {{ str($inquiry->status)->headline() }}
                            </span>
                        </a>
                    @empty
                        <p class="p-5 text-sm text-zinc-500">No client requests yet.</p>
                    @endforelse
                </div>
            </section>
        @endif

        <aside class="admin-card admin-card-plain h-max p-5">
            <p class="admin-eyebrow">Action Summary</p>
            <h2 class="mt-2">What needs attention.</h2>
            <div class="mt-4">
                @if ($canAdmin('pipeline.manage'))
                    <a href="{{ route('admin.inquiries.index', ['status' => 'new']) }}" class="admin-action-row">
                        <span>
                            <span class="block text-sm font-extrabold text-zinc-950">New requests</span>
                            <span class="block text-xs text-zinc-500">Client inquiries not touched</span>
                        </span>
                        <span class="admin-status-number">{{ number_format($stats['new_inquiries']) }}</span>
                    </a>
                    <a href="{{ route('admin.inquiries.index', ['due' => 1]) }}" class="admin-action-row">
                        <span>
                            <span class="block text-sm font-extrabold text-zinc-950">Follow-ups due</span>
                            <span class="block text-xs text-zinc-500">Scheduled client actions</span>
                        </span>
                        <span class="admin-status-number">{{ number_format($stats['due_followups']) }}</span>
                    </a>
                @endif
                @if ($canAdmin('providers.manage'))
                    <a href="{{ route('admin.providers.index', ['status' => 'pending']) }}" class="admin-action-row">
                        <span>
                            <span class="block text-sm font-extrabold text-zinc-950">Provider review</span>
                            <span class="block text-xs text-zinc-500">Approve or verify listings</span>
                        </span>
                        <span class="admin-status-number">{{ number_format($stats['pending_providers']) }}</span>
                    </a>
                @endif
                @if ($canAdmin('reviews.manage'))
                    <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="admin-action-row">
                        <span>
                            <span class="block text-sm font-extrabold text-zinc-950">Review queue</span>
                            <span class="block text-xs text-zinc-500">Moderation before public display</span>
                        </span>
                        <span class="admin-status-number">{{ number_format($stats['reviews_pending']) }}</span>
                    </a>
                @endif
            </div>
        </aside>
    </div>
@endsection
