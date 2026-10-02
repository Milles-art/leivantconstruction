@extends('layouts.seo')

@section('title', 'Find Construction Providers Tanzania | Leivant Discovery')
@section('meta_description', 'Find verified construction providers in Tanzania: contractors, engineers, architects, material suppliers, equipment rental, and skilled labour.')
@section('canonical', route('discovery.index'))

@section('content')
<div class="home-v2">

    {{-- HERO --}}
    <section class="proj-hero" aria-label="Provider discovery">
        <img src="{{ asset('images/site/hero-construction-site.jpg') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);" data-reveal>Leivant Discovery</p>
            <h1 data-reveal>Find or list <span class="amber">construction providers.</span></h1>
            <p class="ed-lead" data-reveal>Search verified contractors, suppliers, equipment rental teams, engineers, architects, and skilled trades across Tanzania.</p>
            <div class="ed-hero-cta" data-reveal>
                <a href="{{ route('register') }}" class="ed-btn ed-btn-solid">Register as provider</a>
                <a href="{{ route('login') }}" class="ed-btn ed-btn-ghost">Provider login</a>
            </div>
        </div>
    </section>

    {{-- BOARD --}}
    <section class="ed-section" aria-label="Provider board">
        <div class="ed-wrap">
            <div class="cm-card" data-reveal>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
                    <div>
                        <span class="cm-tag">Live board</span>
                        <h2 style="font-size:clamp(24px,3vw,32px);margin-top:10px;">{{ count($providers) }} verified provider posts</h2>
                        <p class="cm-muted" style="margin-top:6px;font-size:13px;">Approved providers appear here after Leivant review.</p>
                    </div>
                    <a class="cm-btn-outline" href="{{ route('register') }}">List your business</a>
                </div>

                <form method="GET" action="{{ route('discovery.index') }}" class="cm-filter" style="margin-top:20px;">
                    <label class="search-field">
                        <span>Search posts</span>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Supplier, contractor, district, material...">
                    </label>
                    <label>
                        <span>Type</span>
                        <select name="category">
                            <option value="">All types</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Region</span>
                        <select name="region">
                            <option value="">Any region</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region }}" @selected(request('region') === $region)>{{ $region }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="cm-btn" type="submit">Search</button>
                    <a class="cm-btn-outline" href="{{ route('discovery.index') }}">Reset</a>
                </form>

                <div class="pv-grid" style="margin-top:20px;">
                    @forelse ($providers as $provider)
                        <x-provider-card :provider="$provider" />
                    @empty
                        <div class="resources-empty">
                            @if (! request()->filled('search') && ! request()->filled('category') && ! request()->filled('region'))
                                <h2>Search verified providers</h2>
                                <p>Enter a location or trade category above to find verified providers across Tanzania.</p>
                            @else
                                <h2>No matching providers</h2>
                                <p>Try a different category, region, or search word. New providers can register and wait for Leivant review.</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
