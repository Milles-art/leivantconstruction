@extends('layouts.seo')

@section('title', 'Edit Provider Profile | Leivant Construction Solutions')
@section('meta_description', 'Update a Leivant provider profile for review and Discovery visibility.')
@section('canonical', route('provider.profile.edit'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="cm-page">
    <section class="proj-hero" aria-label="Edit provider profile">
        <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);">Provider profile</p>
            <h1>Update your <span class="amber">business details.</span></h1>
            <p class="ed-lead">Keep your profile accurate so Leivant can review your business and connect client requests by region, category, and service need.</p>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-wrap cm-authsplit">
            <aside class="form-card">
                <p class="ed-label">Review notes</p>
                <h2 style="font-size:26px;margin-top:10px;">Clear details help matching.</h2>
                <div style="display:grid;gap:12px;margin-top:20px;">
                    @foreach ([
                        'Profile edits return to Leivant review before public Discovery visibility.',
                        'Use a real operating location and describe where your team can serve.',
                        'Clear service descriptions help Leivant understand what requests fit your business.',
                    ] as $item)
                        <div class="cm-note">{{ $item }}</div>
                    @endforeach
                </div>
            </aside>

            <div class="form-card">
                <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
                    <div>
                        <p class="ed-label">Business information</p>
                        <h2 style="font-size:26px;margin-top:10px;">{{ $provider->name }}</h2>
                    </div>
                    <a href="{{ route('dashboard') }}" class="cm-btn-outline" style="text-decoration:none;">Back to Dashboard</a>
                </div>

                @if ($errors->any())
                    <div class="cm-note danger" style="margin-top:18px;">
                        <strong>Please correct the highlighted details.</strong>
                        <ul style="margin:8px 0 0;padding-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('provider.profile.update') }}" class="form-grid" style="margin-top:24px;">
                    @csrf
                    @method('PATCH')

                    <label>
                        <span>Business / provider name</span>
                        <input name="provider_name" value="{{ old('provider_name', $provider->name) }}" required autofocus>
                    </label>

                    <label>
                        <span>Provider category</span>
                        <select name="category" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category', $provider->category) === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Region</span>
                        <select name="region_id" required>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" @selected((int) old('region_id', $provider->region_id) === $region->id)>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Operating location</span>
                        <input name="location" value="{{ old('location', $provider->location) }}" placeholder="District, town, or service area">
                    </label>

                    <label>
                        <span>Company email</span>
                        <input type="email" name="email" value="{{ old('email', $provider->email) }}" required>
                    </label>

                    <label>
                        <span>Company phone</span>
                        <input name="phone" value="{{ old('phone', $provider->phone) }}" required>
                    </label>

                    <label class="full">
                        <span>What do you supply or provide?</span>
                        <textarea name="description" required>{{ old('description', $provider->description) }}</textarea>
                    </label>

                    <div class="cm-note full">
                        After saving, this profile will be marked for Leivant review before it is shown publicly in Discovery.
                    </div>

                    <button class="cm-btn full" style="grid-column:1/-1;" type="submit">Save profile for review</button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
