@extends('layouts.seo')

@section('title', 'Provider Login | Leivant Construction Solutions')
@section('meta_description', 'Login for Leivant administrators and approved construction providers.')
@section('canonical', route('login'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="cm-page">
    <section class="proj-hero" aria-label="Login">
        <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);">Leivant access</p>
            <h1>Provider and <span class="amber">admin login.</span></h1>
            <p class="ed-lead">Customers can browse, request quotes, use the planner, and shop without an account. Login is reserved for Leivant teams and provider accounts.</p>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-wrap cm-authsplit">
            <div>
                <p class="ed-label">Secure access</p>
                <h2 style="font-size:clamp(28px,3.5vw,40px);margin-top:12px;">For operations and provider onboarding.</h2>
                <p class="ed-lead" style="font-size:15px;">Admins manage products, orders, providers, inquiries, and project requests. Providers use accounts to track onboarding and directory status.</p>
                <div style="display:grid;gap:12px;margin-top:24px;">
                    @foreach ([
                        'Customers do not need accounts to request construction services.',
                        'Providers can register and wait for Leivant review.',
                        'Admins use the same login to manage the control panel.',
                    ] as $item)
                        <div class="cm-note">{{ $item }}</div>
                    @endforeach
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="form-card">
                @csrf
                <p class="ed-label">Login</p>
                <h2 style="font-size:26px;margin-top:10px;">Welcome back</h2>

                @if ($errors->any())
                    <div class="cm-note danger" style="margin-top:18px;">
                        <strong>Please check the login details.</strong>
                        <ul style="margin:8px 0 0;padding-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-grid" style="margin-top:22px;">
                    <label class="full">
                        <span>Email address</span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="company@example.com" required autofocus autocomplete="username">
                    </label>
                    <label class="full">
                        <span>Password</span>
                        <input type="password" name="password" required autocomplete="current-password">
                    </label>
                    <label class="full cm-remember">
                        <input type="checkbox" name="remember">
                        <strong>Remember this device</strong>
                    </label>
                    <button class="cm-btn full" style="grid-column:1/-1;" type="submit">Login to account</button>
                </div>
                <p class="cm-muted" style="text-align:center;margin-top:18px;font-size:13px;">Sell materials, rent equipment, or provide construction services? <a href="{{ route('register') }}" style="color:#92400e;font-weight:800;">Register your business</a></p>
            </form>
        </div>
    </section>
</div>
@endsection
