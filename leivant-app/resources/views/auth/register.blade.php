@extends('layouts.seo')

@section('title', 'Provider Registration | Leivant Construction Solutions')
@section('meta_description', 'Register a construction supplier, contractor, equipment rental business, engineer, architect, or skilled trade provider with Leivant Discovery.')
@section('canonical', route('register'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="cm-page">
    <section class="proj-hero" aria-label="Provider registration">
        <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);">Provider onboarding</p>
            <h1>Register your <span class="amber">construction business.</span></h1>
            <p class="ed-lead">This form is for suppliers, equipment rental businesses, contractors, skilled teams, site support teams, engineers, architects, logistics providers, and installation teams across Tanzania.</p>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-wrap cm-authsplit">
            <aside>
                <p class="ed-label">How registration works</p>
                <h2 style="font-size:clamp(28px,3.5vw,40px);margin-top:12px;">Reviewed before public listing.</h2>
                <div class="cm-steps" style="grid-template-columns:1fr;margin-top:24px;">
                    @foreach ([
                        'Submit business details, service category, region, and contact information.',
                        'Leivant reviews the profile for quality and relevance.',
                        'Approved providers become searchable in Discovery by region and category.',
                    ] as $i => $step)
                        <div class="cm-step">
                            <span class="n">{{ sprintf('%02d', $i + 1) }}</span>
                            <p>{{ $step }}</p>
                        </div>
                    @endforeach
                </div>
            </aside>

            <form method="POST" action="{{ route('register') }}" class="form-card">
                @csrf
                <p class="ed-label">Business account</p>
                <h2 style="font-size:26px;margin-top:10px;">Provider registration</h2>
                <p class="cm-note" style="margin-top:18px;">New profiles are reviewed by Leivant before they appear publicly in Discovery.</p>

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

                <div class="form-grid" style="margin-top:22px;">
                    <label>
                        <span>Business / provider name</span>
                        <input name="provider_name" value="{{ old('provider_name') }}" placeholder="Example: Mwanza Steel Traders" required autofocus>
                    </label>
                    <label>
                        <span>Provider category</span>
                        <select name="category" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Region</span>
                        <select name="region_id" required>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Operating location</span>
                        <input name="location" value="{{ old('location') }}" placeholder="District, town, or service area">
                    </label>
                    <label>
                        <span>Company email</span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="company@example.com" required autocomplete="username">
                    </label>
                    <label>
                        <span>Company phone</span>
                        <input name="phone" value="{{ old('phone') }}" placeholder="+255..." required>
                    </label>
                    <label class="full">
                        <span>What do you supply or provide?</span>
                        <textarea name="description" required placeholder="Example: We supply cement and blocks in Mwanza, rent compactors in Tanga, or provide electrical installation teams in Kilimanjaro.">{{ old('description') }}</textarea>
                    </label>
                    <label>
                        <span>Password</span>
                        <input type="password" name="password" required autocomplete="new-password">
                    </label>
                    <label>
                        <span>Confirm password</span>
                        <input type="password" name="password_confirmation" required autocomplete="new-password">
                    </label>
                    <button class="cm-btn full" style="grid-column:1/-1;" type="submit">Create provider account</button>
                </div>
                <p class="cm-muted" style="text-align:center;margin-top:18px;font-size:13px;">Already registered as a provider or admin? <a href="{{ route('login') }}" style="color:#92400e;font-weight:800;">Login</a></p>
            </form>
        </div>
    </section>
</div>
@endsection
