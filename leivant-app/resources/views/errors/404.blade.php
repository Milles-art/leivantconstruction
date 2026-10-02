@extends('layouts.seo')

@section('title', 'Page Not Found | Leivant Construction')
@section('meta_description', 'The page you requested could not be found. Continue to Leivant services, marketplace, project support, or contact the construction desk in Dar es Salaam.')
@section('canonical', route('home'))
@section('robots', 'noindex,follow')

@section('content')
    <section class="page-hero not-found-hero">
        <div class="section-inner not-found-grid">
            <div>
                <span class="section-label">404</span>
                <h1>Page Not Found</h1>
                <p class="lead">The page may have moved, but the Leivant Construction Desk is still here to help with design, BOQ planning, materials, equipment rental, and project delivery across Tanzania.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('home') }}">Go Home</a>
                    <a class="btn btn-outline" href="{{ route('contact.index') }}">Contact Leivant</a>
                </div>
            </div>
            <div class="not-found-links" aria-label="Helpful links">
                <a href="{{ route('services.index') }}">Construction Services</a>
                <a href="{{ route('products.index') }}">Marketplace</a>
                <a href="{{ route('projects.index') }}">Projects</a>
                <a href="{{ route('discovery.index') }}">Provider Discovery</a>
            </div>
        </div>
    </section>
@endsection

@push('head')
    <style>
        .not-found-hero { min-height: 520px; background: var(--color-surface); }
        .not-found-grid { display:grid; grid-template-columns:minmax(0, 1fr) 320px; gap:42px; align-items:center; }
        .not-found-links { display:grid; gap:12px; }
        .not-found-links a { display:flex; align-items:center; min-height:54px; border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-white); padding:0 18px; color:var(--color-dark); font-size:14px; font-weight:800; box-shadow:var(--shadow-card); }
        .not-found-links a:hover { color:var(--color-primary-dark); border-color:var(--color-primary); }
        @media (max-width: 760px) {
            .not-found-grid { grid-template-columns:1fr; gap:28px; }
        }
    </style>
@endpush