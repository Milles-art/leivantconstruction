@extends('layouts.seo')

@section('og_image', asset('leivant-webp/20260608_162620.jpg.webp'))

@section('title', 'Construction Services Tanzania | Leivant Construction Solutions')
@section('meta_description', 'Architecture, engineering, construction delivery, site support, and skilled labour services across Tanzania from Leivant.')
@section('canonical', route('services.index'))

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

  <div class="home-v2">

    {{-- HERO --}}
    <section class="proj-hero" aria-label="Services introduction">
      <img src="{{ asset('leivant-webp/20260523_141609.jpg.webp') }}"
           alt="Active Leivant construction site in Tanzania"
           width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      <div class="ed-wrap proj-hero-inner">
        <p class="ed-label" style="color:var(--amber);" data-reveal>Construction services</p>
        <h1 data-reveal>From first brief to final handover — <span class="amber">every construction service</span> under one company.</h1>
        <p class="ed-lead" data-reveal>
          Leivant plans, builds, renovates, and supervises projects across Tanzania — coordinating architecture, engineering, materials, equipment, labour, and site delivery through one accountable team.
        </p>
        <p data-reveal><span class="svc-badge">BRELA 626936 · Registered Tanzanian construction company</span></p>
        <div class="ed-hero-cta" data-reveal>
          <a href="{{ route('contact.index') }}" class="ed-btn ed-btn-solid">Request a quote</a>
          <a href="#main-services" class="ed-btn ed-btn-ghost">Browse all services</a>
        </div>
      </div>
    </section>

    {{-- INTRO STRIP --}}
    <section class="ed-section ed-sand" style="padding-top:64px;padding-bottom:64px;" aria-label="Overview">
      <div class="ed-wrap">
        <div class="svc-intro-grid">
          @foreach ($intro as $i => [$title, $body])
            <article data-reveal>
              <span class="svc-intro-num">{{ sprintf('%02d', $i + 1) }}</span>
              <h3>{{ $title }}</h3>
              <p>{{ $body }}</p>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    {{-- CORE SERVICE LINES --}}
    <section id="main-services" class="ed-section" style="scroll-margin-top:120px;" aria-label="Core services">
      <div class="ed-wrap">
        <p class="ed-label" data-reveal>Core service lines</p>
        <h2 class="ed-h2" data-reveal>Professional services for real project delivery</h2>
        <p class="ed-lead" data-reveal>Each service line connects to the same Leivant project desk — so your brief, costing, sourcing, and site work stay aligned.</p>
        <div class="svc-lines">
          @foreach ($lines as $i => [$image, $title, $body, $tags])
            <article class="svc-line{{ $i % 2 === 1 ? ' flip' : '' }}" data-reveal>
              <div class="svc-line-media">
                <img src="{{ asset($image) }}" alt="{{ $title }}" loading="lazy" decoding="async">
                <span class="svc-line-num">{{ sprintf('%02d', $i + 1) }}</span>
              </div>
              <div class="svc-line-body">
                <h3>{{ $title }}</h3>
                <p>{{ $body }}</p>
                <div class="svc-tags">
                  @foreach ($tags as $tag)
                    <span>{{ $tag }}</span>
                  @endforeach
                </div>
                <p><a class="ed-link" href="{{ route('contact.index') }}">Discuss this service →</a></p>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    {{-- FULL SERVICE LIST --}}
    <section class="ed-section svc-detail" aria-label="Detailed services">
      <div class="ed-wrap">
        <div style="text-align:center;" data-reveal>
          <p class="ed-label" style="justify-content:center;">Detailed services</p>
          <h2 class="ed-h2" style="margin-left:auto;margin-right:auto;color:white;">Everything included from start to finish</h2>
          <p class="ed-lead" style="margin-left:auto;margin-right:auto;color:rgba(255,255,255,0.7);">Twelve focused services Leivant can review, plan, coordinate, or deliver after understanding your project scope.</p>
        </div>
        <div class="svc-detail-grid">
          @foreach ($services as [$code, $title, $body])
            <article data-reveal>
              <div class="svc-code">{{ $code }}</div>
              <div>
                <h3>{{ $title }}</h3>
                <p>{{ $body }}</p>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="ed-section" aria-label="How it works">
      <div class="ed-wrap svc-how-grid">
        <div data-reveal>
          <p class="ed-label">How it works</p>
          <h2 class="ed-h2">Four steps from brief to supervised execution</h2>
          <ol class="ed-steps" style="margin-top:40px;">
            @foreach ($steps as $i => [$title, $body])
              <li data-reveal>
                <span class="ed-step-num">{{ $i + 1 }}</span>
                <div class="ed-step-card">
                  <strong>{{ $title }}</strong>
                  <p>{{ $body }}</p>
                </div>
              </li>
            @endforeach
          </ol>
        </div>
        <div class="svc-how-media" data-reveal>
          <img src="{{ asset('leivant-webp/20260523_141714.jpg.webp') }}"
               alt="Leivant supervised construction site in Tanzania"
               loading="lazy" decoding="async">
        </div>
      </div>
    </section>

    {{-- CLOSING CTA (shared editorial CTA) --}}
    <section class="ed-section ed-cta" aria-label="Contact">
      <div class="ed-wrap" data-reveal>
        <h2>Ready to scope your project with Leivant?</h2>
        <p class="ed-lead">Send your brief and our team will review site conditions, service fit, and the practical next step for your build.</p>
        <div class="ed-contact-row">
          <a href="tel:+255717970799">0717 970 799</a>
          <a href="https://wa.me/255717970799" target="_blank" rel="noopener">WhatsApp</a>
          <a href="mailto:info@leivantconstruction.com">info@leivantconstruction.com</a>
        </div>
        <a href="{{ route('contact.index') }}" class="ed-btn-dark">Send project brief</a>
      </div>
    </section>

  </div>
@endsection
