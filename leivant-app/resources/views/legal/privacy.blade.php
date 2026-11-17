@extends('layouts.seo')

@section('title', 'Privacy Policy | Leivant Construction Solutions')
@section('meta_description', 'Leivant privacy policy for project inquiries, marketplace orders, provider discovery, construction planning, and client communication.')
@section('canonical', route('privacy'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Privacy policy introduction">
      <div class="hv-hero__media">
        <img src="{{ asset('leivant-webp/20260608_162620.jpg.webp') }}" alt="Leivant construction site in Tanzania" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">Privacy Policy</p>
        <h1 class="hv-hero__title">How Leivant handles <span class="hv-amber">project information.</span></h1>
        <p class="hv-hero__lead">We collect only the details needed to review construction requests, coordinate providers, process marketplace orders, and respond to clients professionally.</p>
        <div class="hv-hero__actions">
          <a href="#policy" class="hv-btn">Read the policy</a>
          <a href="{{ route('contact.index') }}" class="hv-btn hv-btn--ghost">Contact Leivant</a>
        </div>
      </div>
    </section>

    {{-- POLICY DETAILS --}}
    <section id="policy" class="hv-section hv-section--surface" style="scroll-margin-top:120px;" aria-label="Policy details">
      <div class="hv-wrap">
        <div class="hv-head">
          <p class="hv-eyebrow">Policy overview</p>
          <h2 class="hv-title">Clear, limited, and purposeful.</h2>
          <p class="hv-lead">The points below summarise how project information is collected, used, shared, and how to reach Leivant with a privacy question.</p>
        </div>
        <div class="hv-grid hv-grid--2">
          @foreach ([
              ['Information We Collect', 'Name, phone, email, project location, site details, service needs, equipment orders, preferred contact method, and provider registration details where applicable.'],
              ['How We Use It', 'We use submitted information to contact clients, prepare quotations, coordinate providers, review checkout requests, arrange payment instructions, and improve Leivant service operations.'],
              ['Sharing', 'Project details may be shared with relevant contractors, suppliers, equipment providers, technical teams, or logistics partners only where needed to support the request.'],
              ['Contact', 'For privacy questions, contact '.config('app.company.email').' or '.config('app.company.phone').'.'],
          ] as [$heading, $body])
            <article class="hv-card">
              <span class="hv-card__num">{{ sprintf('%02d', $loop->iteration) }}</span>
              <h3 class="hv-card__title">{{ $heading }}</h3>
              <p class="hv-card__text">{{ $body }}</p>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
      <h2 class="hv-cta__title">Questions about your information?</h2>
      <p class="hv-cta__text">Reach the Leivant team directly and we will review your privacy question and respond through your preferred contact channel.</p>
      <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
        <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
        <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
        <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
      </div>
      <a href="{{ route('contact.index') }}" class="hv-btn">Contact Leivant</a>
    </section>

  </div>
@endsection
