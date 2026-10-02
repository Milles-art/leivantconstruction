@extends('layouts.seo')

@section('title', 'Account Dashboard | Leivant Construction Solutions')
@section('meta_description', 'Leivant account dashboard for administrators and provider onboarding.')
@section('canonical', route('dashboard'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="cm-page">
    @php
        $user = auth()->user();
        $provider = $user->provider;
        $approved = $provider && $provider->is_active && $provider->is_verified;
        $profileFields = $provider ? [
            'Business name' => filled($provider->name),
            'Category' => filled($provider->category),
            'Region' => filled($provider->region_id),
            'Phone' => filled($provider->phone),
            'Email' => filled($provider->email),
            'Location' => filled($provider->location),
            'Description' => filled($provider->description),
        ] : [];
        $completedFields = collect($profileFields)->filter()->count();
        $completionPercent = count($profileFields) ? (int) round(($completedFields / count($profileFields)) * 100) : 0;
    @endphp

    <section class="proj-hero" aria-label="Account dashboard">
        <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);">{{ $user->is_admin ? 'Admin account' : 'Provider account' }}</p>
            <h1>Welcome, <span class="amber">{{ $user->name }}.</span></h1>
            <p class="ed-lead">
                @if ($user->is_admin)
                    Manage Leivant operations, provider reviews, service requests, orders, products, and customer inquiries from one account.
                @elseif ($approved)
                    Your provider profile is approved and visible in Discovery for matching client searches.
                @else
                    Your provider account has been received. Leivant reviews new profiles before they appear publicly in Discovery.
                @endif
            </p>
            <div class="ed-hero-cta">
                @if ($user->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="ed-btn ed-btn-solid">Open admin panel</a>
                @else
                    <a href="{{ route('discovery.index') }}" class="ed-btn ed-btn-solid">View Discovery</a>
                    @if ($provider)
                        <a href="{{ route('provider.profile.edit') }}" class="ed-btn ed-btn-ghost">Edit provider profile</a>
                    @endif
                @endif
                <form method="POST" action="{{ route('logout') }}" data-no-loader>
                    @csrf
                    <button class="ed-btn ed-btn-ghost" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-wrap cm-dashgrid">
            @if ($user->is_admin)
                <aside class="form-card">
                    <p class="ed-label">Control center</p>
                    <h2 style="font-size:26px;margin-top:10px;">Operations shortcuts.</h2>
                    <p class="cm-muted" style="margin-top:12px;font-size:14px;">Jump into the main admin workspaces without leaving the client-side visual system.</p>
                </aside>
                <div class="cm-grid" style="grid-template-columns:repeat(2,minmax(0,1fr));">
                    @foreach ([
                        ['Admin Dashboard', route('admin.dashboard'), 'View platform stats and recent activity.'],
                        ['Providers', route('admin.providers.index'), 'Approve, verify, and manage supplier profiles.'],
                        ['Orders', route('admin.orders.index'), 'Review checkout and equipment requests.'],
                        ['Inquiries', route('admin.inquiries.index'), 'Respond to service and contact requests.'],
                    ] as [$label, $url, $body])
                        <a href="{{ $url }}" class="cm-card" style="text-decoration:none;">
                            <span class="cm-tag">{{ $label }}</span>
                            <p class="cm-muted" style="margin-top:10px;font-size:14px;">{{ $body }}</p>
                            <span class="ed-link">Open →</span>
                        </a>
                    @endforeach
                </div>
            @else
                <aside class="form-card">
                    <p class="ed-label">Provider status</p>
                    <h2 style="font-size:26px;margin-top:10px;">{{ $approved ? 'Approved profile.' : 'Pending Leivant review.' }}</h2>
                    <p class="cm-muted" style="margin-top:12px;font-size:14px;">{{ $approved ? 'Clients can find your business in Discovery.' : 'Your listing stays private until Leivant completes review.' }}</p>
                    <div style="margin-top:18px;">
                        <span class="cm-status {{ $approved ? 'good' : 'waiting' }}">{{ $approved ? 'Visible in Discovery' : 'Not public yet' }}</span>
                    </div>
                </aside>

                <div style="display:grid;gap:16px;">
                    @if ($provider)
                        <article class="form-card">
                            <div style="display:flex;gap:18px;align-items:start;justify-content:space-between;flex-wrap:wrap;">
                                <div>
                                    <p class="ed-label">Profile completeness</p>
                                    <h2 style="font-size:32px;margin-top:10px;">{{ $completionPercent }}% complete</h2>
                                    <p class="cm-muted" style="margin-top:8px;font-size:14px;">{{ $completedFields }} of {{ count($profileFields) }} details completed.</p>
                                </div>
                                <a href="{{ route('provider.profile.edit') }}" class="cm-btn" style="text-decoration:none;">Update Business Profile</a>
                            </div>
                            <div class="cm-track">
                                <div class="cm-fill" style="width: {{ $completionPercent }}%"></div>
                            </div>
                        </article>

                        <div class="cm-metricrow">
                            @foreach ([
                                ['Business', $provider->name],
                                ['Category', $provider->category],
                                ['Region', $provider->region?->name],
                            ] as [$label, $value])
                                <article class="cm-metric">
                                    <span class="cm-tag">{{ $label }}</span>
                                    <h3 style="margin-top:12px;font-size:22px;">{{ $value ?: 'Not Set' }}</h3>
                                </article>
                            @endforeach
                        </div>

                        <article class="form-card">
                            <p class="ed-label">Profile description</p>
                            <p class="cm-muted" style="margin-top:10px;">{{ $provider->description }}</p>
                            <div class="cm-actions">
                                <a href="{{ route('discovery.index', ['search' => $provider->name]) }}" class="cm-btn-outline" style="text-decoration:none;">Check Discovery Listing</a>
                            </div>
                        </article>
                    @endif

                    <div class="cm-grid" style="grid-template-columns:repeat(3,minmax(0,1fr));">
                        @foreach ([
                            ['Profile Review', $approved ? 'Complete' : 'Waiting', $approved ? 'Leivant has approved your listing.' : 'Leivant checks provider details before publishing.'],
                            ['Discovery Visibility', $approved ? 'Active' : 'Hidden', $approved ? 'Clients can find your profile.' : 'Your profile is not shown publicly yet.'],
                            ['Next Step', $approved ? 'Respond Fast' : 'Keep Contact Ready', $approved ? 'Leivant may connect clients by request.' : 'Leivant may contact you for clarification.'],
                        ] as [$label, $value, $body])
                            <article class="cm-card">
                                <span class="cm-tag">{{ $label }}</span>
                                <h3 style="margin-top:12px;font-size:24px;">{{ $value }}</h3>
                                <p class="cm-muted" style="font-size:14px;margin-top:6px;">{{ $body }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
