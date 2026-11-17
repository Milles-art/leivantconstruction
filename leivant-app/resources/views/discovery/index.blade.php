@extends('layouts.seo')

@section('title', 'Find Construction Providers Tanzania | Leivant Discovery')
@section('meta_description', 'Find verified construction providers in Tanzania: contractors, engineers, architects, material suppliers, equipment rental, and skilled labour.')
@section('canonical', route('discovery.index'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Provider discovery">
      <div class="hv-hero__media">
        <img src="{{ asset('images/site/hero-construction-site.jpg') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">Leivant Discovery</p>
        <h1 class="hv-hero__title">Find or list <span class="hv-amber">construction providers.</span></h1>
        <p class="hv-hero__lead">Search verified contractors, suppliers, equipment rental teams, engineers, architects, and skilled trades across Tanzania.</p>
        <div class="hv-hero__actions">
          <a href="{{ route('register') }}" class="hv-btn">Register as provider</a>
          <a href="{{ route('login') }}" class="hv-btn hv-btn--ghost">Provider login</a>
        </div>
      </div>
    </section>

    {{-- STATS --}}
    <section class="hv-section hv-section--tight" aria-label="Board overview">
      <div class="hv-wrap">
        <div class="hv-stats">
          <div class="hv-stat">
            <div class="hv-stat__value">{{ count($providers) }}</div>
            <div class="hv-stat__label">Verified provider posts</div>
          </div>
          <div class="hv-stat">
            <div class="hv-stat__value">{{ count($categories) }}</div>
            <div class="hv-stat__label">Categories</div>
          </div>
          <div class="hv-stat">
            <div class="hv-stat__value">{{ count($regions) }}</div>
            <div class="hv-stat__label">Regions</div>
          </div>
          <div class="hv-stat">
            <div class="hv-stat__value">TZ</div>
            <div class="hv-stat__label">Across Tanzania</div>
          </div>
        </div>
      </div>
    </section>

    {{-- BOARD --}}
    <section class="hv-section hv-section--surface" aria-label="Provider board">
      <div class="hv-wrap">
        <div class="hv-head">
          <p class="hv-eyebrow">Live board</p>
          <h2 class="hv-title">{{ count($providers) }} verified provider posts</h2>
          <p class="hv-lead">Approved providers appear here after Leivant review.</p>
          <a class="hv-btn hv-mt" href="{{ route('register') }}">List your business</a>
        </div>

        <form method="GET" action="{{ route('discovery.index') }}" class="hv-form">
          <div class="hv-form__row">
            <label class="hv-field">
              <span>Search posts</span>
              <input type="search" name="search" value="{{ request('search') }}" placeholder="Supplier, contractor, district, material...">
            </label>
            <label class="hv-field">
              <span>Type</span>
              <select name="category">
                <option value="">All types</option>
                @foreach ($categories as $category)
                  <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
              </select>
            </label>
            <label class="hv-field">
              <span>Region</span>
              <select name="region">
                <option value="">Any region</option>
                @foreach ($regions as $region)
                  <option value="{{ $region }}" @selected(request('region') === $region)>{{ $region }}</option>
                @endforeach
              </select>
            </label>
          </div>
          <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button class="hv-btn" type="submit">Search</button>
            <a class="hv-btn hv-btn--ghost" href="{{ route('discovery.index') }}">Reset</a>
          </div>
        </form>

        <div class="hv-grid hv-grid--3 hv-mt">
          @forelse ($providers as $provider)
            <x-provider-card :provider="$provider" />
          @empty
            <article class="hv-card" style="grid-column:1 / -1;min-height:0;">
              @if (! request()->filled('search') && ! request()->filled('category') && ! request()->filled('region'))
                <h3 class="hv-card__title">Search verified providers</h3>
                <p class="hv-card__text">Enter a location or trade category above to find verified providers across Tanzania.</p>
              @else
                <h3 class="hv-card__title">No matching providers</h3>
                <p class="hv-card__text">Try a different category, region, or search word. New providers can register and wait for Leivant review.</p>
              @endif
            </article>
          @endforelse
        </div>
      </div>
    </section>

    {{-- HOW DISCOVERY WORKS --}}
    <section class="hv-section" aria-label="How discovery works">
      <div class="hv-wrap">
        <div class="hv-head">
          <p class="hv-eyebrow">Leivant Discovery</p>
          <h2 class="hv-title">Search verified contractors, suppliers and skilled trades.</h2>
          <p class="hv-lead">Search verified contractors, suppliers, equipment rental teams, engineers, architects, and skilled trades across Tanzania.</p>
        </div>
        <div class="hv-grid hv-grid--3">
          <article class="hv-card">
            <span class="hv-card__num">01</span>
            <h3 class="hv-card__title">Search posts</h3>
            <p class="hv-card__text">Supplier, contractor, district, material — search the board for the provider your project needs.</p>
          </article>
          <article class="hv-card">
            <span class="hv-card__num">02</span>
            <h3 class="hv-card__title">Type &amp; region</h3>
            <p class="hv-card__text">Filter by provider type and region to narrow the list to verified local capacity.</p>
          </article>
          <article class="hv-card">
            <span class="hv-card__num">03</span>
            <h3 class="hv-card__title">List your business</h3>
            <p class="hv-card__text">Approved providers appear here after Leivant review.</p>
          </article>
        </div>
      </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Get started">
      <h2 class="hv-cta__title">Find or list construction providers.</h2>
      <p class="hv-cta__text">Search verified contractors, suppliers, equipment rental teams, engineers, architects, and skilled trades across Tanzania.</p>
      <div class="hv-hero__actions" style="justify-content:center;">
        <a href="{{ route('register') }}" class="hv-btn">Register as provider</a>
        <a href="{{ route('login') }}" class="hv-btn hv-btn--ghost">Provider login</a>
      </div>
    </section>

  </div>
@endsection
