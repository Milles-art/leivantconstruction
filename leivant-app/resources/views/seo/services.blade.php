@extends('layouts.seo')

@section('og_image', asset('leivant-webp/20260608_162620.jpg.webp'))
@section('title', 'Construction Services Tanzania | Leivant Construction Solutions')
@section('meta_description', 'Architecture, engineering, construction delivery, site support, and skilled labour services across Tanzania from Leivant.')
@section('canonical', route('services.index'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
  @php
    $intro = [
      ['One company, all phases', 'Planning, BOQ, materials, equipment, labour, and supervised delivery — coordinated by the same Leivant team from brief to handover.'],
      ['Residential to commercial', 'New builds, extensions, renovations, commercial fit-outs, and civil site works across Dar es Salaam, Zanzibar, and upcountry.'],
      ['Cost clarity upfront', 'BOQ preparation and estimation before ground breaks — so clients understand real project costs before committing.'],
      ['Active site supervision', 'Progress checks, quality control, and practical reporting — not a build handed off and forgotten.'],
    ];
    $lines = [
      ['leivant-webp/20260523_141830.jpg.webp', 'Architecture & design', 'Our team prepares concept drawings, working plans, permit-ready documentation, space layout planning, and BOQ support — matched to your site, approval requirements, and build budget.', ['Concept drawings', 'Permit-ready plans', 'Layout planning', 'BOQ support']],
      ['leivant-webp/20260608_162620.jpg.webp', 'Construction & delivery', 'Leivant manages and executes construction on site — foundations, structural frames, roofing, interior and exterior finishing, renovation scopes, and supervised site progress from start to handover.', ['New builds', 'Renovations', 'Roofing & finishing', 'Site supervision']],
      ['leivant-webp/20260608_162631.jpg.webp', 'Engineering & technical', 'Structural review, foundation guidance, civil works advice, drainage planning, MEP coordination, and site inspections that give your project a technically sound and safe base.', ['Structural review', 'Foundation guidance', 'Site inspection', 'MEP coordination']],
      ['leivant-webp/20260608_162548.jpg.webp', 'Site support', 'Reliable on-site assistance for material handling, concrete mixing, loading, trenching, site cleaning, excavation support, and supervised general site tasks.', ['Material handling', 'Site cleaning', 'Concrete mixing', 'Excavation assistance']],
      ['leivant-webp/IMG-20260529-WA0066.jpg.webp', 'Skilled labour', 'Vetted trade teams — masons, steel fixers, carpenters, plumbers, electricians, painters, gypsum installers, and tile setters — matched and scheduled to your project timeline.', ['Masonry', 'Steel fixing', 'Plumbing & electrical', 'Tiles, gypsum, painting']],
    ];
    $services = [
      ['HP', 'House planning & design', 'Leivant reviews your site, plot size, and requirements to prepare a workable house plan matched to your budget and approval path.'],
      ['2D', '2D floor plan design', 'Clear architectural floor plans showing room layout, dimensions, door and window positions, and circulation paths for your build.'],
      ['3D', '3D house visualization', 'Rendered views of your proposed house so you can see finishes, proportions, and layout before construction begins.'],
      ['AR', 'Architectural design', 'Full architectural documentation from concept through permit-ready drawings prepared around your site conditions and local authority requirements.'],
      ['CE', 'Construction cost estimation', 'Detailed cost breakdown covering materials, labour, equipment, and site works based on your specific scope and location.'],
      ['BQ', 'BOQ preparation', 'Itemised Bill of Quantities listing every material and trade required, with quantities confirmed against your drawings and site conditions.'],
      ['MQ', 'Material quantity estimation', 'Calculated material takeoffs for cement, sand, blocks, steel, roofing, and finishing items based on your approved drawings.'],
      ['RF', 'Roofing design services', 'Structural and architectural design for pitched, flat, or trussed roofing systems matched to your structure, loads, and weather exposure.'],
      ['EL', 'Electrical layout planning', 'Planned circuit routing, socket and switch positions, panel location, and load schedules for a safe and compliant electrical installation.'],
      ['PL', 'Plumbing layout planning', 'Planned pipe routing, fixture positions, waste drainage, and connection points for a properly functioning plumbing system.'],
      ['ST', 'Structural planning', 'Foundation design, column and beam sizing, and slab specifications reviewed to suit your soil conditions and building loads.'],
      ['CS', 'Construction supervision', 'Regular site visits, progress checks, quality reviews, and reporting to keep your build on track and to specification.'],
    ];
    $steps = [
      ['Submit your brief', 'Share location, scope, drawings, and timeline through call, WhatsApp, email, or our contact form.'],
      ['We assess', 'Leivant reviews site access, quantities, materials, labour, and equipment fit — including a site visit when needed.'],
      ['Clear construction route', 'You receive a practical scope outline, costing approach, and supervised next steps before any commitment.'],
      ['Supervised execution', 'Materials, labour, and progress are coordinated by the same Leivant team through delivery.'],
    ];
  @endphp

  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Services introduction">
      <div class="hv-hero__media">
        <img src="{{ asset('leivant-webp/20260523_141609.jpg.webp') }}" alt="Active Leivant construction site in Tanzania" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">Construction services</p>
        <h1 class="hv-hero__title">From first brief to final handover — <span class="hv-amber">every construction service</span> under one company.</h1>
        <p class="hv-hero__lead">Leivant plans, builds, renovates, and supervises projects across Tanzania — coordinating architecture, engineering, materials, equipment, labour, and site delivery through one accountable team.</p>
        <p class="hv-mono hv-muted" style="margin-top:20px;font-size:11px;">BRELA 626936 · Registered Tanzanian construction company</p>
        <div class="hv-hero__actions">
          <a href="{{ route('contact.index') }}" class="hv-btn">Request a quote</a>
          <a href="#main-services" class="hv-btn hv-btn--ghost">Browse all services</a>
        </div>
      </div>
    </section>

    {{-- INTRO STRIP --}}
    <section class="hv-section hv-section--tight" aria-label="Overview">
      <div class="hv-wrap">
        <div class="hv-grid hv-grid--4">
          @foreach ($intro as $i => [$title, $body])
            <article class="hv-card">
              <span class="hv-card__num">{{ sprintf('%02d', $i + 1) }}</span>
              <h3 class="hv-card__title">{{ $title }}</h3>
              <p class="hv-card__text">{{ $body }}</p>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    {{-- CORE SERVICE LINES --}}
    <section id="main-services" class="hv-section hv-section--surface" style="scroll-margin-top:120px;" aria-label="Core services">
      <div class="hv-wrap">
        <div class="hv-head">
          <p class="hv-eyebrow">Core service lines</p>
          <h2 class="hv-title">Professional services for real project delivery</h2>
          <p class="hv-lead">Each service line connects to the same Leivant project desk — so your brief, costing, sourcing, and site work stay aligned.</p>
        </div>
        <div class="hv-grid hv-grid--2">
          @foreach ($lines as $i => [$image, $title, $body, $tags])
            <article class="hv-photo-card" style="min-height:440px;">
              <img src="{{ asset($image) }}" alt="{{ $title }}" loading="lazy" decoding="async">
              <span class="hv-photo-card__num">{{ sprintf('%02d', $i + 1) }}</span>
              <h3 class="hv-photo-card__title">{{ $title }}</h3>
              <p class="hv-photo-card__text">{{ $body }}</p>
              <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:16px;">
                @foreach ($tags as $tag)
                  <span class="hv-pill">{{ $tag }}</span>
                @endforeach
              </div>
              <a class="hv-card__link" href="{{ route('contact.index') }}" aria-label="Discuss {{ $title }}"></a>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    {{-- FULL SERVICE LIST --}}
    <section class="hv-section" aria-label="Detailed services">
      <div class="hv-wrap">
        <div class="hv-head hv-center" style="margin-inline:auto;">
          <p class="hv-eyebrow" style="justify-content:center;">Detailed services</p>
          <h2 class="hv-title">Everything included from start to finish</h2>
          <p class="hv-lead" style="margin-inline:auto;">Twelve focused services Leivant can review, plan, coordinate, or deliver after understanding your project scope.</p>
        </div>
        <div class="hv-grid hv-grid--4">
          @foreach ($services as [$code, $title, $body])
            <article class="hv-card">
              <span class="hv-card__num">{{ $code }}</span>
              <h3 class="hv-card__title">{{ $title }}</h3>
              <p class="hv-card__text">{{ $body }}</p>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="hv-section hv-section--surface" aria-label="How it works">
      <div class="hv-wrap hv-split">
        <div>
          <p class="hv-eyebrow">How it works</p>
          <h2 class="hv-title">Four steps from brief to supervised execution</h2>
          <ol class="hv-list hv-mt" style="counter-reset:step;">
            @foreach ($steps as $i => [$title, $body])
              <li>
                <span class="hv-amber hv-mono" style="font-size:12px;">{{ sprintf('%02d', $i + 1) }}</span>
                <div><strong style="color:var(--hv-text);font-family:var(--hv-display);font-size:22px;text-transform:uppercase;">{{ $title }}</strong><p>{{ $body }}</p></div>
              </li>
            @endforeach
          </ol>
        </div>
        <div class="hv-split__media">
          <img src="{{ asset('leivant-webp/20260523_141714.jpg.webp') }}" alt="Leivant supervised construction site in Tanzania" loading="lazy" decoding="async">
        </div>
      </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
      <h2 class="hv-cta__title">Ready to scope your project?</h2>
      <p class="hv-cta__text">Send your brief and our team will review site conditions, service fit, and the practical next step for your build.</p>
      <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
        <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
        <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
        <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
      </div>
      <a href="{{ route('contact.index') }}" class="hv-btn">Send project brief</a>
    </section>

  </div>
@endsection
