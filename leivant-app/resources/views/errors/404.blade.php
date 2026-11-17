@extends('layouts.seo')

@section('title', 'Page Not Found | Leivant Construction')
@section('meta_description', 'The page you requested could not be found. Continue to Leivant services, marketplace, project support, or contact the construction desk in Dar es Salaam.')
@section('canonical', route('home'))
@section('robots', 'noindex,follow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
  <div class="hv-page">
    <section class="hv-hero" style="min-height:640px;" aria-label="Page not found">
      <div class="hv-hero__media"><img src="{{ asset('leivant-webp/20260523_141609.jpg.webp') }}" alt="Leivant construction site" loading="eager" decoding="async"></div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">404 · Page not found</p>
        <h1 class="hv-hero__title">This page has moved on.</h1>
        <p class="hv-hero__lead">The page may have moved, but the Leivant Construction Desk is still here to help with design, BOQ planning, materials, equipment rental, and project delivery across Tanzania.</p>
        <div class="hv-hero__actions">
          <a href="{{ route('home') }}" class="hv-btn">Go home</a>
          <a href="{{ route('contact.index') }}" class="hv-btn hv-btn--ghost">Contact Leivant</a>
        </div>
      </div>
    </section>

    <section class="hv-section hv-section--surface" aria-label="Helpful links">
      <div class="hv-wrap">
        <div class="hv-head">
          <p class="hv-eyebrow">Keep exploring</p>
          <h2 class="hv-title">Find your way back.</h2>
        </div>
        <div class="hv-grid hv-grid--4">
          <a class="hv-card" href="{{ route('services.index') }}"><h3 class="hv-card__title">Construction services</h3></a>
          <a class="hv-card" href="{{ route('products.index') }}"><h3 class="hv-card__title">Marketplace</h3></a>
          <a class="hv-card" href="{{ route('projects.index') }}"><h3 class="hv-card__title">Projects</h3></a>
          <a class="hv-card" href="{{ route('discovery.index') }}"><h3 class="hv-card__title">Provider discovery</h3></a>
        </div>
      </div>
    </section>

    <section class="hv-cta" aria-label="Contact">
      <h2 class="hv-cta__title">Need a hand finding something?</h2>
      <p class="hv-cta__text">Tell the Leivant desk what you need and we will point you in the right direction.</p>
      <a href="{{ route('contact.index') }}" class="hv-btn">Contact Leivant</a>
    </section>
  </div>
@endsection
