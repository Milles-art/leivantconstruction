@extends('layouts.seo')

@section('title', 'Account Dashboard | Leivant Construction Solutions')
@section('meta_description', 'Leivant account dashboard for administrators and provider onboarding.')
@section('canonical', route('dashboard'))
@section('robots', 'noindex, nofollow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
<div class="hv-page">
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

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Account dashboard">
        <div class="hv-hero__media">
            <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        </div>
        <div class="hv-hero__inner">
            <p class="hv-eyebrow">{{ $user->is_admin ? 'Admin account' : 'Provider account' }}</p>
            <h1 class="hv-hero__title">Welcome, <span class="hv-amber">{{ $user->name }}.</span></h1>
            <p class="hv-hero__lead">
                @if ($user->is_admin)
                    Manage Leivant operations, provider reviews, service requests, orders, products, and customer inquiries from one account.
                @elseif ($approved)
                    Your provider profile is approved and visible in Discovery for matching client searches.
                @else
                    Your provider account has been received. Leivant reviews new profiles before they appear publicly in Discovery.
                @endif
            </p>
            <div class="hv-hero__actions">
                @if ($user->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="hv-btn">Open admin panel</a>
                @else
                    <a href="{{ route('discovery.index') }}" class="hv-btn">View Discovery</a>
                    @if ($provider)
                        <a href="{{ route('provider.profile.edit') }}" class="hv-btn hv-btn--ghost">Edit provider profile</a>
                    @endif
                @endif
                <form method="POST" action="{{ route('logout') }}" data-no-loader>
                    @csrf
                    <button class="hv-btn hv-btn--ghost" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </section>

    {{-- DASHBOARD --}}
    <section class="hv-section" aria-label="Account overview">
        <div class="hv-wrap">
            @if ($user->is_admin)
                <div class="hv-grid hv-grid--2">
                    <aside class="hv-card">
                        <p class="hv-eyebrow">Control center</p>
                        <h3 class="hv-card__title">Operations shortcuts.</h3>
                        <p class="hv-card__text" style="margin-top:12px;">Jump into the main admin workspaces without leaving the client-side visual system.</p>
                    </aside>
                    <div style="display:grid;gap:16px;">
                        @foreach ([
                            ['Admin Dashboard', route('admin.dashboard'), 'View platform stats and recent activity.'],
                            ['Providers', route('admin.providers.index'), 'Approve, verify, and manage supplier profiles.'],
                            ['Orders', route('admin.orders.index'), 'Review checkout and equipment requests.'],
                            ['Inquiries', route('admin.inquiries.index'), 'Respond to service and contact requests.'],
                        ] as [$label, $url, $body])
                            <a href="{{ $url }}" class="hv-card" style="min-height:0;text-decoration:none;">
                                <span class="hv-pill">{{ $label }}</span>
                                <p class="hv-card__text" style="margin-top:10px;">{{ $body }}</p>
                                <span class="hv-amber hv-mono" style="margin-top:auto;font-size:11px;">Open →</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="hv-grid hv-grid--2">
                    <aside class="hv-card">
                        <p class="hv-eyebrow">Provider status</p>
                        <h3 class="hv-card__title">{{ $approved ? 'Approved profile.' : 'Pending Leivant review.' }}</h3>
                        <p class="hv-card__text" style="margin-top:12px;">{{ $approved ? 'Clients can find your business in Discovery.' : 'Your listing stays private until Leivant completes review.' }}</p>
                        <div style="margin-top:18px;">
                            <span class="hv-pill">{{ $approved ? 'Visible in Discovery' : 'Not public yet' }}</span>
                        </div>
                    </aside>

                    <div style="display:grid;gap:16px;">
                        @if ($provider)
                            <article class="hv-card" style="min-height:0;">
                                <div style="display:flex;gap:18px;align-items:start;justify-content:space-between;flex-wrap:wrap;">
                                    <div>
                                        <p class="hv-eyebrow">Profile completeness</p>
                                        <h3 class="hv-card__title" style="font-size:clamp(32px,3.5vw,48px);">{{ $completionPercent }}% complete</h3>
                                        <p class="hv-card__text" style="margin-top:8px;">{{ $completedFields }} of {{ count($profileFields) }} details completed.</p>
                                    </div>
                                    <a href="{{ route('provider.profile.edit') }}" class="hv-btn">Update Business Profile</a>
                                </div>
                                <div style="height:8px;background:rgba(255,255,255,.1);border-radius:999px;overflow:hidden;margin-top:24px;">
                                    <div style="width: {{ $completionPercent }}%;height:100%;background:var(--hv-amber);border-radius:999px;"></div>
                                </div>
                            </article>

                            <div class="hv-grid hv-grid--3">
                                @foreach ([
                                    ['Business', $provider->name],
                                    ['Category', $provider->category],
                                    ['Region', $provider->region?->name],
                                ] as [$label, $value])
                                    <article class="hv-card" style="min-height:0;">
                                        <span class="hv-pill">{{ $label }}</span>
                                        <h3 class="hv-card__title" style="font-size:22px;margin-top:12px;">{{ $value ?: 'Not Set' }}</h3>
                                    </article>
                                @endforeach
                            </div>

                            <article class="hv-card" style="min-height:0;">
                                <p class="hv-eyebrow">Profile description</p>
                                <p class="hv-card__text" style="margin-top:10px;">{{ $provider->description }}</p>
                                <div style="margin-top:18px;">
                                    <a href="{{ route('discovery.index', ['search' => $provider->name]) }}" class="hv-btn hv-btn--ghost">Check Discovery Listing</a>
                                </div>
                            </article>
                        @endif

                        <div class="hv-grid hv-grid--3">
                            @foreach ([
                                ['Profile Review', $approved ? 'Complete' : 'Waiting', $approved ? 'Leivant has approved your listing.' : 'Leivant checks provider details before publishing.'],
                                ['Discovery Visibility', $approved ? 'Active' : 'Hidden', $approved ? 'Clients can find your profile.' : 'Your profile is not shown publicly yet.'],
                                ['Next Step', $approved ? 'Respond Fast' : 'Keep Contact Ready', $approved ? 'Leivant may connect clients by request.' : 'Leivant may contact you for clarification.'],
                            ] as [$label, $value, $body])
                                <article class="hv-card" style="min-height:0;">
                                    <span class="hv-pill">{{ $label }}</span>
                                    <h3 class="hv-card__title" style="font-size:24px;margin-top:12px;">{{ $value }}</h3>
                                    <p class="hv-card__text" style="font-size:14px;margin-top:6px;">{{ $body }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Discovery">
        <h2 class="hv-cta__title">Explore Leivant Discovery</h2>
        <p class="hv-cta__text">Browse reviewed construction providers, suppliers, and skilled teams by region and service category across Tanzania.</p>
        <a href="{{ route('discovery.index') }}" class="hv-btn">View Discovery</a>
    </section>
</div>
@endsection
