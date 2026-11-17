@extends('layouts.seo')

@section('title', $service['name'].' Tanzania | Leivant Construction Services')
@section('meta_description', $service['summary'] ?? 'Professional construction services, planning, BOQ support, project coordination, and provider matching in Tanzania.')
@section('canonical', route('services.show', $service['slug']))
@section('og_image', $service['image'])

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Service overview">
      <div class="hv-hero__media">
        <img src="{{ $service['image'] }}" alt="{{ match (basename(parse_url($service['image'], PHP_URL_PATH) ?? '')) {
            'hero-construction-site.jpg' => 'Active construction site supervised by Leivant Construction Solutions',
            'road-civil-works.jpg' => 'Civil works and road construction coordinated through Leivant Tanzania',
            'engineering-review.jpg' => 'Structural engineering review and site inspection by Leivant',
            'materials-supply.jpg' => 'Building materials including cement, sand, blocks, and steel available through Leivant Marketplace',
            'electrical-plumbing.jpg' => 'Power tools and electrical equipment for construction sites in Tanzania',
            'project-management.jpg' => 'Construction project management and site coordination in Tanzania',
            default => $service['name'],
        } }}" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">{{ $service['provider_category'] }}</p>
        <h1 class="hv-hero__title">{{ $service['name'] }}</h1>
        <p class="hv-hero__lead">{{ $service['description'] }}</p>
        <div class="hv-hero__actions">
          <a href="#service-request" class="hv-btn">Submit Service Request</a>
          <a href="{{ route('services.index') }}" class="hv-btn hv-btn--ghost">All Services</a>
        </div>
      </div>
    </section>

    {{-- DELIVERABLES / BEST FOR --}}
    <section class="hv-section hv-section--surface" aria-label="Service fit">
      <div class="hv-wrap">
        <div class="hv-grid hv-grid--2">
          <article class="hv-card">
            <span class="hv-card__num">01</span>
            <p class="hv-eyebrow">What Leivant Delivers</p>
            <h2 class="hv-card__title" style="margin-top:16px;">Clear service output.</h2>
            <ul class="hv-list" style="margin-top:20px;">
              @foreach ($service['deliverables'] ?? $service['features'] as $item)
                <li>{{ $item }}</li>
              @endforeach
            </ul>
          </article>

          <article class="hv-card">
            <span class="hv-card__num">02</span>
            <p class="hv-eyebrow">Best For</p>
            <h2 class="hv-card__title" style="margin-top:16px;">Where this service fits.</h2>
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:20px;">
              @foreach ($service['best_for'] ?? [] as $item)
                <span class="hv-pill">{{ $item }}</span>
              @endforeach
            </div>
          </article>
        </div>
      </div>
    </section>

    {{-- PROCESS + REQUEST --}}
    <section class="hv-section" aria-label="Service process and request">
      <div class="hv-wrap hv-split" style="align-items:start;">
        <div>
          <div class="hv-head">
            <p class="hv-eyebrow">Service Process</p>
            <h2 class="hv-title">How Leivant handles the request.</h2>
            <p class="hv-lead">Every request is reviewed around site location, stage, urgency, available documents, and expected outcome before a professional next step is recommended.</p>
          </div>
          <div class="hv-grid hv-grid--2">
            @foreach ($service['process'] ?? [] as $step)
              <article class="hv-card" style="min-height:180px;">
                <p class="hv-card__text">{{ $step }}</p>
              </article>
            @endforeach
          </div>
        </div>

        <aside id="service-request" class="hv-card" style="padding:clamp(22px,3vw,40px);scroll-margin-top:120px;">
          <p class="hv-eyebrow">Request {{ $service['name'] }}</p>
          <h2 class="hv-card__title" style="margin:16px 0 12px;">Send Leivant the project brief.</h2>
          <p class="hv-lead" style="font-size:16px;">No account is required. Leivant Construction Desk will review the details and respond through your preferred contact channel.</p>

          @if ($errors->any())
            <div class="hv-card" style="margin-top:18px;border-color:var(--hv-amber);">
              <strong class="hv-amber hv-mono">Please check the form.</strong>
              <ul class="hv-list">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('services.request', $service['slug']) }}" class="hv-form" style="margin-top:22px;">
            @csrf
            <div class="hv-form__row">
              <label class="hv-field">
                <span>Full Name</span>
                <input name="name" value="{{ old('name') }}" required>
              </label>
              <label class="hv-field">
                <span>Company / Client Name</span>
                <input name="company" value="{{ old('company') }}">
              </label>
            </div>
            <div class="hv-form__row">
              <label class="hv-field">
                <span>Phone / WhatsApp</span>
                <input name="phone" value="{{ old('phone') }}" required>
              </label>
              <label class="hv-field">
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}">
              </label>
            </div>
            <div class="hv-form__row">
              <label class="hv-field">
                <span>Region</span>
                <select name="region" required>
                  <option value="">Select region</option>
                  @foreach ($regions as $region)
                    <option value="{{ $region }}" @selected(old('region') === $region)>{{ $region }}</option>
                  @endforeach
                </select>
              </label>
              <label class="hv-field">
                <span>Site Location</span>
                <input name="site_location" value="{{ old('site_location') }}" placeholder="Example: Salasala, Kinondoni" required>
              </label>
            </div>
            <div class="hv-form__row">
              <label class="hv-field">
                <span>Project Type</span>
                <select name="project_type" required>
                  <option value="">Select project type</option>
                  @foreach ($service['project_types'] ?? [$service['name']] as $type)
                    <option value="{{ $type }}" @selected(old('project_type') === $type)>{{ $type }}</option>
                  @endforeach
                </select>
              </label>
              <label class="hv-field">
                <span>Project Stage</span>
                <select name="project_stage">
                  <option value="">Select stage</option>
                  @foreach ($requestOptions['stages'] as $stage)
                    <option value="{{ $stage }}" @selected(old('project_stage') === $stage)>{{ $stage }}</option>
                  @endforeach
                </select>
              </label>
            </div>
            <div class="hv-form__row">
              <label class="hv-field">
                <span>Budget Range</span>
                <select name="budget_range">
                  <option value="">Select budget range</option>
                  @foreach ($requestOptions['budget_ranges'] as $range)
                    <option value="{{ $range }}" @selected(old('budget_range') === $range)>{{ $range }}</option>
                  @endforeach
                </select>
              </label>
              <label class="hv-field">
                <span>Timeline</span>
                <select name="timeline" required>
                  <option value="">Select timeline</option>
                  @foreach ($requestOptions['timelines'] as $timeline)
                    <option value="{{ $timeline }}" @selected(old('timeline') === $timeline)>{{ $timeline }}</option>
                  @endforeach
                </select>
              </label>
            </div>
            <label class="hv-field" style="grid-column:1 / -1;">
              <span>Preferred Contact</span>
              <select name="preferred_contact" required>
                @foreach (['whatsapp' => 'WhatsApp', 'phone' => 'Phone Call', 'email' => 'Email'] as $value => $label)
                  <option value="{{ $value }}" @selected(old('preferred_contact', 'whatsapp') === $value)>{{ $label }}</option>
                @endforeach
              </select>
            </label>
            <label class="hv-field" style="grid-column:1 / -1;">
              <span>Project Message</span>
              <textarea name="message" required placeholder="Describe the project, site condition, expected work, or challenge Leivant should review.">{{ old('message') }}</textarea>
            </label>
            <button class="hv-btn" type="submit" style="width:100%;">Submit Service Request</button>
          </form>
        </aside>
      </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
      <h2 class="hv-cta__title">Ready to scope your project?</h2>
      <p class="hv-cta__text">Send your brief and the Leivant Construction Desk will review site conditions, service fit, and the practical next step for your build.</p>
      <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
        <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
        <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
        <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
      </div>
      <a href="{{ route('contact.index') }}" class="hv-btn">Send project brief</a>
    </section>

  </div>
@endsection
