@extends('layouts.seo')

@section('title', 'Provider Login | Leivant Construction Solutions')
@section('meta_description', 'Login for Leivant administrators and approved construction providers.')
@section('canonical', route('login'))
@section('robots', 'noindex, nofollow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
<div class="hv-page">
    {{-- HERO --}}
    <section class="hv-hero" aria-label="Login">
        <div class="hv-hero__media">
            <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        </div>
        <div class="hv-hero__inner">
            <p class="hv-eyebrow">Leivant access</p>
            <h1 class="hv-hero__title">Provider and <span class="hv-amber">admin login.</span></h1>
            <p class="hv-hero__lead">Customers can browse, request quotes, use the planner, and shop without an account. Login is reserved for Leivant teams and provider accounts.</p>
            <div class="hv-hero__actions">
                <a href="{{ route('home') }}" class="hv-btn hv-btn--ghost">Back to home</a>
            </div>
        </div>
    </section>

    {{-- LOGIN --}}
    <section class="hv-section hv-section--surface" aria-label="Secure access">
        <div class="hv-wrap">
            <div class="hv-head">
                <p class="hv-eyebrow">Secure access</p>
                <h2 class="hv-title">For operations and provider onboarding.</h2>
                <p class="hv-lead">Admins manage products, orders, providers, inquiries, and project requests. Providers use accounts to track onboarding and directory status.</p>
                <ul class="hv-list hv-mt">
                    @foreach ([
                        'Customers do not need accounts to request construction services.',
                        'Providers can register and wait for Leivant review.',
                        'Admins use the same login to manage the control panel.',
                    ] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <div style="max-width:560px;margin-inline:auto;width:100%;">
                <form method="POST" action="{{ route('login') }}" class="hv-card" style="min-height:0;">
                    @csrf
                    <p class="hv-eyebrow">Login</p>
                    <h3 class="hv-card__title">Welcome back</h3>

                    @if ($errors->any())
                        <div class="hv-card" style="min-height:0;border-color:#ef7b6b;margin-top:18px;">
                            <strong class="hv-amber">Please check the login details.</strong>
                            <ul class="hv-list hv-mt" style="margin-top:8px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="hv-form" style="margin-top:22px;">
                        <label class="hv-field">
                            <span>Email address</span>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="company@example.com" required autofocus autocomplete="username">
                        </label>
                        <label class="hv-field">
                            <span>Password</span>
                            <input type="password" name="password" required autocomplete="current-password">
                        </label>
                        <label class="hv-field" style="grid-template-columns:auto 1fr;align-items:center;">
                            <input type="checkbox" name="remember" style="width:auto;min-height:0;">
                            <strong class="hv-muted" style="font-family:var(--hv-font);font-size:14px;text-transform:none;letter-spacing:0;">Remember this device</strong>
                        </label>
                        <button class="hv-btn" style="width:100%;" type="submit">Login to account</button>
                    </div>
                    <p class="hv-muted hv-center" style="margin-top:18px;font-size:13px;">Sell materials, rent equipment, or provide construction services? <a href="{{ route('register') }}" class="hv-amber" style="font-weight:800;">Register your business</a></p>
                </form>
            </div>
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Resources">
        <h2 class="hv-cta__title">Need construction resources?</h2>
        <p class="hv-cta__text">Customers can browse materials, tools, and equipment and send a request to Leivant without creating an account.</p>
        <a href="{{ route('products.index') }}" class="hv-btn">Browse Resources</a>
    </section>
</div>
@endsection
