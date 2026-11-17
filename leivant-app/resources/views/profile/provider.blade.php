@extends('layouts.seo')

@section('title', 'Edit Provider Profile | Leivant Construction Solutions')
@section('meta_description', 'Update a Leivant provider profile for review and Discovery visibility.')
@section('canonical', route('provider.profile.edit'))
@section('robots', 'noindex, nofollow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
<div class="hv-page">
    {{-- HERO --}}
    <section class="hv-hero" aria-label="Edit provider profile">
        <div class="hv-hero__media">
            <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        </div>
        <div class="hv-hero__inner">
            <p class="hv-eyebrow">Provider profile</p>
            <h1 class="hv-hero__title">Update your <span class="hv-amber">business details.</span></h1>
            <p class="hv-hero__lead">Keep your profile accurate so Leivant can review your business and connect client requests by region, category, and service need.</p>
            <div class="hv-hero__actions">
                <a href="{{ route('dashboard') }}" class="hv-btn hv-btn--ghost">Back to Dashboard</a>
            </div>
        </div>
    </section>

    {{-- PROFILE FORM --}}
    <section class="hv-section hv-section--surface" aria-label="Provider profile form">
        <div class="hv-wrap">
            <div class="hv-head">
                <p class="hv-eyebrow">Review notes</p>
                <h2 class="hv-title">Clear details help matching.</h2>
                <ul class="hv-list hv-mt">
                    @foreach ([
                        'Profile edits return to Leivant review before public Discovery visibility.',
                        'Use a real operating location and describe where your team can serve.',
                        'Clear service descriptions help Leivant understand what requests fit your business.',
                    ] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <div style="max-width:760px;margin-inline:auto;width:100%;">
                <div class="hv-card" style="min-height:0;">
                    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
                        <div>
                            <p class="hv-eyebrow">Business information</p>
                            <h3 class="hv-card__title">{{ $provider->name }}</h3>
                        </div>
                        <a href="{{ route('dashboard') }}" class="hv-btn hv-btn--ghost">Back to Dashboard</a>
                    </div>

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

                    <form method="POST" action="{{ route('provider.profile.update') }}" class="hv-form" style="margin-top:24px;">
                        @csrf
                        @method('PATCH')

                        <label class="hv-field">
                            <span>Business / provider name</span>
                            <input name="provider_name" value="{{ old('provider_name', $provider->name) }}" required autofocus>
                        </label>

                        <div class="hv-form__row">
                            <label class="hv-field">
                                <span>Provider category</span>
                                <select name="category" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}" @selected(old('category', $provider->category) === $category)>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="hv-field">
                                <span>Region</span>
                                <select name="region_id" required>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}" @selected((int) old('region_id', $provider->region_id) === $region->id)>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <label class="hv-field">
                            <span>Operating location</span>
                            <input name="location" value="{{ old('location', $provider->location) }}" placeholder="District, town, or service area">
                        </label>

                        <div class="hv-form__row">
                            <label class="hv-field">
                                <span>Company email</span>
                                <input type="email" name="email" value="{{ old('email', $provider->email) }}" required>
                            </label>

                            <label class="hv-field">
                                <span>Company phone</span>
                                <input name="phone" value="{{ old('phone', $provider->phone) }}" required>
                            </label>
                        </div>

                        <label class="hv-field">
                            <span>What do you supply or provide?</span>
                            <textarea name="description" required>{{ old('description', $provider->description) }}</textarea>
                        </label>

                        <p class="hv-card__text">After saving, this profile will be marked for Leivant review before it is shown publicly in Discovery.</p>

                        <button class="hv-btn" style="width:100%;" type="submit">Save profile for review</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Discovery">
        <h2 class="hv-cta__title">See how clients find you</h2>
        <p class="hv-cta__text">Approved provider profiles appear in Leivant Discovery, where clients search by region, category, and service need.</p>
        <a href="{{ route('discovery.index') }}" class="hv-btn">View Discovery</a>
    </section>
</div>
@endsection
