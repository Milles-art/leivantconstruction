@extends('layouts.seo')

@section('title', 'Terms of Service | Leivant Construction Solutions')
@section('meta_description', 'Terms for using Leivant construction services, provider discovery, equipment sourcing, product checkout, and construction support.')
@section('canonical', route('terms'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Terms of service introduction">
      <div class="hv-hero__media">
        <img src="{{ asset('leivant-webp/20260523_141830.jpg.webp') }}" alt="Leivant supervised construction site in Tanzania" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">Terms of Service</p>
        <h1 class="hv-hero__title">Terms for using <span class="hv-amber">Leivant services.</span></h1>
        <p class="hv-hero__lead">These terms apply when clients use Leivant for construction services, provider discovery, equipment sourcing, product checkout, technical support, and site logistics.</p>
        <div class="hv-hero__actions">
          <a href="#terms" class="hv-btn">Read the terms</a>
          <a href="{{ route('contact.index') }}" class="hv-btn hv-btn--ghost">Contact Leivant</a>
        </div>
      </div>
    </section>

    {{-- TERMS DETAILS --}}
    <section id="terms" class="hv-section hv-section--surface" style="scroll-margin-top:120px;" aria-label="Terms details">
      <div class="hv-wrap">
        <div class="hv-head">
          <p class="hv-eyebrow">Service terms</p>
          <h2 class="hv-title">How Leivant services are engaged.</h2>
          <p class="hv-lead">The points below summarise how requests, rentals, and provider connections are reviewed and confirmed before work proceeds.</p>
        </div>
        <div class="hv-grid hv-grid--2">
          @foreach ([
              ['Requests and Quotations', 'Submitted requests are reviewed by Leivant before pricing, availability, rental scheduling, payment instructions, or provider connection is finalized.'],
              ['Products and Rentals', 'Equipment availability, condition, rental dates, payment instructions, site requirements, and final approval details must be confirmed before fulfillment.'],
              ['Provider Connections', 'Leivant helps connect clients with relevant providers, but project owners remain responsible for approving final scope, cost, contract terms, and site access.'],
              ['Contact', 'For terms questions, contact '.config('app.company.email').' or '.config('app.company.phone').'.'],
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
      <h2 class="hv-cta__title">Need a term clarified?</h2>
      <p class="hv-cta__text">Reach the Leivant team directly and we will review your question and respond through your preferred contact channel.</p>
      <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
        <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
        <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
        <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
      </div>
      <a href="{{ route('contact.index') }}" class="hv-btn">Contact Leivant</a>
    </section>

  </div>
@endsection
