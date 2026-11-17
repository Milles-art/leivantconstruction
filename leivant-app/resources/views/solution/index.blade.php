@extends('layouts.app')

@section('title', 'One Company Desk for Construction Services | Leivant')
@section('meta_description', 'Leivant coordinates construction services, equipment, labour, providers, and site support through one company-led workflow across Tanzania.')
@section('canonical', route('solution.index'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
    @php
        $siteImage = fn (string $name) => asset("images/site/{$name}.jpg");
        $painPoints = [
            'Clients struggle to know who is reliable before money or materials are committed.',
            'Design, labour, tools, suppliers, supervision, and delivery are often handled in separate conversations.',
            'Regional and international clients need one trusted Tanzania-based coordination point.',
            'Projects lose time when quotation, site readiness, equipment, and communication are not aligned.',
        ];
        $flow = [
            ['Request', 'Client shares the service, equipment, provider, labour, or site-support need.'],
            ['Review', 'Leivant checks project location, scope, documents, budget range, and current readiness.'],
            ['Plan', 'The right service path, team, equipment option, or provider connection is organized.'],
            ['Confirm', 'Pricing, rental dates, delivery plan, timeline, and responsibilities are clarified.'],
            ['Deliver', 'The service, equipment, team, or approved connection is coordinated to the project site.'],
            ['Follow Up', 'Progress checks and client feedback help keep the work accountable.'],
        ];
        $helpGroups = [
            ['title' => 'Clients', 'body' => 'Homeowners, developers, institutions, contractors, diaspora clients, and international partners can start with one clear project brief instead of chasing many contacts.', 'image' => $siteImage('solution-clients')],
            ['title' => 'Providers', 'body' => 'Approved suppliers, engineers, architects, contractors, equipment businesses, and skilled teams gain a professional route to relevant construction requests.', 'image' => $siteImage('international-client-support')],
            ['title' => 'Projects', 'body' => 'Buildings, road works, renovations, installations, rental equipment, materials, site support, and supervision are handled with better coordination.', 'image' => $siteImage('road-civil-works')],
        ];
        $coordination = ['Building construction','3D design and drawings','Engineering review','Skilled labour','Site support','Equipment rental','Materials support','Project supervision','Provider discovery'];
        $trustPoints = ['One company communication desk','Structured service request forms','Leivant-reviewed providers','Company-managed equipment catalog','Order and inquiry tracking','Tanzania presence with international-client readiness'];
    @endphp

    <div class="hv-page">

        {{-- HERO --}}
        <section class="hv-hero" aria-label="Solution introduction">
            <div class="hv-hero__media"><img src="{{ $siteImage('contract-execution') }}" alt="Construction planning documents and project coordination" width="900" height="600" loading="eager" decoding="async" fetchpriority="high"></div>
            <div class="hv-hero__inner">
                <p class="hv-eyebrow">Leivant Solution</p>
                <h1 class="hv-hero__title">One company desk for construction services.</h1>
                <p class="hv-hero__lead">Leivant helps clients move from a project need to organized action by coordinating services, equipment, labour, providers, site support, and follow-up through one company-led workflow.</p>
                <div class="hv-hero__actions">
                    <a href="{{ route('contact.index') }}" class="hv-btn">Request project support</a>
                    <a href="{{ route('services.index') }}" class="hv-btn hv-btn--ghost">Explore services</a>
                </div>
            </div>
        </section>

        {{-- PROBLEM --}}
        <section class="hv-section hv-section--surface" aria-label="The problem">
            <div class="hv-wrap hv-split">
                <div>
                    <p class="hv-eyebrow">The problem</p>
                    <h2 class="hv-title">Construction slows down when every decision lives in a different place.</h2>
                    <p class="hv-lead">Leivant exists to reduce confusion before work starts and to make the next practical step easier to understand.</p>
                </div>
                <div class="hv-grid hv-grid--2">
                    @foreach ($painPoints as $point)
                        <article class="hv-card" style="min-height:220px;">
                            <span class="hv-card__num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <p class="hv-card__text" style="font-size:16px;">{{ $point }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- METHOD --}}
        <section class="hv-section" aria-label="The Leivant method">
            <div class="hv-wrap">
                <div class="hv-head">
                    <p class="hv-eyebrow">The Leivant method</p>
                    <h2 class="hv-title">A practical route from first request to site follow-up.</h2>
                </div>
                <div class="hv-grid hv-grid--3">
                    @foreach ($flow as [$title, $body])
                        <article class="hv-card">
                            <span class="hv-card__num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="hv-card__title">{{ $title }}</h3>
                            <p class="hv-card__text">{{ $body }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- WHO IT HELPS --}}
        <section class="hv-section hv-section--surface" aria-label="Who it helps">
            <div class="hv-wrap">
                <div class="hv-head">
                    <p class="hv-eyebrow">Who it helps</p>
                    <h2 class="hv-title">Built for clients, providers, and project teams.</h2>
                    <p class="hv-lead">The solution is not just a directory or a shop. It is a coordination layer for construction decisions that need people, tools, information, and follow-up.</p>
                </div>
                <div class="hv-grid hv-grid--3">
                    @foreach ($helpGroups as $group)
                        <article class="hv-photo-card" style="min-height:440px;">
                            <img src="{{ $group['image'] }}" alt="{{ $group['title'] }}" loading="lazy" decoding="async">
                            <h3 class="hv-photo-card__title">{{ $group['title'] }}</h3>
                            <p class="hv-photo-card__text">{{ $group['body'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- COORDINATION --}}
        <section class="hv-section" aria-label="What Leivant coordinates">
            <div class="hv-wrap hv-split">
                <div>
                    <p class="hv-eyebrow">What Leivant coordinates</p>
                    <h2 class="hv-title">The services, tools, and support around the project.</h2>
                    <p class="hv-lead">Leivant keeps the conversation practical: what is needed, where it is needed, who can deliver it, what it costs, and what should happen next.</p>
                </div>
                <div class="hv-grid hv-grid--3">
                    @foreach ($coordination as $item)
                        <article class="hv-card" style="min-height:150px;">
                            <span class="hv-pill">Leivant coordinates</span>
                            <h3 class="hv-card__title" style="font-size:22px;">{{ $item }}</h3>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- WHY IT WORKS --}}
        <section class="hv-section hv-section--surface" aria-label="Why it works">
            <div class="hv-wrap">
                <div class="hv-head">
                    <p class="hv-eyebrow">Why it works</p>
                    <h2 class="hv-title">Leivant gives construction requests a responsible place to land.</h2>
                    <p class="hv-lead">Instead of leaving clients to compare unverified contacts, guess equipment availability, or chase scattered updates, Leivant turns requests into trackable company communication.</p>
                </div>
                <div class="hv-grid hv-grid--3">
                    @foreach ($trustPoints as $point)
                        <article class="hv-card" style="min-height:160px;">
                            <span class="hv-pill">Trust</span>
                            <p class="hv-card__text" style="margin-top:20px;font-size:16px;">{{ $point }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="hv-cta" aria-label="Contact">
            <h2 class="hv-cta__title">Send Leivant your project brief.</h2>
            <p class="hv-cta__text">Share the project location, service need, timeline, and contact details. The company will review the request and respond with the next practical step.</p>
            <a href="{{ route('contact.index') }}" class="hv-btn">Send project brief</a>
        </section>

    </div>
@endsection
