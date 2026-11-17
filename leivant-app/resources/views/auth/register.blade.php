@extends('layouts.seo')

@section('title', 'Provider Registration | Leivant Construction Solutions')
@section('meta_description', 'Register a construction supplier, contractor, equipment rental business, engineer, architect, or skilled trade provider with Leivant Discovery.')
@section('canonical', route('register'))
@section('robots', 'noindex, nofollow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
<div class="hv-page">
    {{-- HERO --}}
    <section class="hv-hero" aria-label="Provider registration">
        <div class="hv-hero__media">
            <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        </div>
        <div class="hv-hero__inner">
            <p class="hv-eyebrow">Provider onboarding</p>
            <h1 class="hv-hero__title">Register your <span class="hv-amber">construction business.</span></h1>
            <p class="hv-hero__lead">This form is for suppliers, equipment rental businesses, contractors, skilled teams, site support teams, engineers, architects, logistics providers, and installation teams across Tanzania.</p>
            <div class="hv-hero__actions">
                <a href="{{ route('login') }}" class="hv-btn hv-btn--ghost">Already registered? Login</a>
            </div>
        </div>
    </section>

    {{-- REGISTRATION --}}
    <section class="hv-section hv-section--surface" aria-label="Provider registration form">
        <div class="hv-wrap">
            <div class="hv-head">
                <p class="hv-eyebrow">How registration works</p>
                <h2 class="hv-title">Reviewed before public listing.</h2>
                <ul class="hv-list hv-mt">
                    @foreach ([
                        'Submit business details, service category, region, and contact information.',
                        'Leivant reviews the profile for quality and relevance.',
                        'Approved providers become searchable in Discovery by region and category.',
                    ] as $i => $step)
                        <li>
                            <span class="hv-amber hv-mono" style="font-size:12px;">{{ sprintf('%02d', $i + 1) }}</span>
                            <span>{{ $step }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div style="max-width:760px;margin-inline:auto;width:100%;">
                <form method="POST" action="{{ route('register') }}" class="hv-card" style="min-height:0;">
                    @csrf
                    <p class="hv-eyebrow">Business account</p>
                    <h3 class="hv-card__title">Provider registration</h3>
                    <p class="hv-card__text" style="margin-top:18px;">New profiles are reviewed by Leivant before they appear publicly in Discovery.</p>

                    @if ($errors->any())
                        <div class="hv-card" style="min-height:0;border-color:#ef7b6b;margin-top:18px;">
                            <strong class="hv-amber">Please correct the highlighted details.</strong>
                            <ul class="hv-list hv-mt" style="margin-top:8px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="hv-form" style="margin-top:22px;">
                        <div class="hv-form__row">
                            <label class="hv-field">
                                <span>Business / provider name</span>
                                <input name="provider_name" value="{{ old('provider_name') }}" placeholder="Example: Mwanza Steel Traders" required autofocus>
                            </label>
                            <label class="hv-field">
                                <span>Provider category</span>
                                <select name="category" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="hv-form__row">
                            <label class="hv-field">
                                <span>Region</span>
                                <select name="region_id" required>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="hv-field">
                                <span>Operating location</span>
                                <input name="location" value="{{ old('location') }}" placeholder="District, town, or service area">
                            </label>
                        </div>
                        <div class="hv-form__row">
                            <label class="hv-field">
                                <span>Company email</span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="company@example.com" required autocomplete="username">
                            </label>
                            <label class="hv-field">
                                <span>Company phone</span>
                                <input name="phone" value="{{ old('phone') }}" placeholder="+255..." required>
                            </label>
                        </div>
                        <label class="hv-field">
                            <span>What do you supply or provide?</span>
                            <textarea name="description" required placeholder="Example: We supply cement and blocks in Mwanza, rent compactors in Tanga, or provide electrical installation teams in Kilimanjaro.">{{ old('description') }}</textarea>
                        </label>
                        <div class="hv-form__row">
                            <label class="hv-field">
                                <span>Password</span>
                                <input type="password" name="password" required autocomplete="new-password">
                            </label>
                            <label class="hv-field">
                                <span>Confirm password</span>
                                <input type="password" name="password_confirmation" required autocomplete="new-password">
                            </label>
                        </div>
                        <button class="hv-btn" style="width:100%;" type="submit">Create provider account</button>
                    </div>
                    <p class="hv-muted hv-center" style="margin-top:18px;font-size:13px;">Already registered as a provider or admin? <a href="{{ route('login') }}" class="hv-amber" style="font-weight:800;">Login</a></p>
                </form>
            </div>
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Discovery">
        <h2 class="hv-cta__title">Explore approved providers</h2>
        <p class="hv-cta__text">Browse Leivant Discovery to find reviewed suppliers, contractors, rental businesses, and skilled teams by region and category.</p>
        <a href="{{ route('discovery.index') }}" class="hv-btn">View Discovery</a>
    </section>
</div>
@endsection
