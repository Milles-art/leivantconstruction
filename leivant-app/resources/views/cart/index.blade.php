@extends('layouts.seo')

@section('title', 'Request List | Leivant Project Resources')
@section('meta_description', 'Review selected construction resources before sending details to Leivant for availability, delivery, and site-fit review.')
@section('canonical', route('cart.index'))
@section('robots', 'noindex, nofollow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
<div class="hv-page">
    @php($total = $items->sum(fn ($item) => $item['line_total'] ?? ($item['price'] * $item['quantity'] * max(1, $item['rental_days'] ?? 1))))

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Request list">
        <div class="hv-hero__media">
            <img src="{{ asset('leivant-webp/20260523_142200.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        </div>
        <div class="hv-hero__inner">
            <p class="hv-eyebrow">Leivant project resources</p>
            <h1 class="hv-hero__title">Review your <span class="hv-amber">request list.</span></h1>
            <p class="hv-hero__lead">Once you submit, Leivant reviews the requested materials, tools, or equipment against site location, availability, timing, delivery, and project fit before confirming the next step.</p>
            <div class="hv-hero__actions">
                <a href="{{ route('products.index') }}" class="hv-btn">View Resources</a>
                <a href="#request-items" class="hv-btn hv-btn--ghost">Review items</a>
            </div>
        </div>
    </section>

    {{-- REQUEST ITEMS --}}
    <section id="request-items" class="hv-section" style="scroll-margin-top:120px;" aria-label="Request items">
        <div class="hv-wrap">
            <div class="hv-head">
                <p class="hv-eyebrow">Request list</p>
                <h2 class="hv-title">Selected resources</h2>
                <p class="hv-lead">Check quantities, rental dates, and line values before sending your request to the Leivant Construction Desk.</p>
            </div>

            @if ($items->isEmpty())
                <div class="hv-card hv-center" style="max-width:720px;margin-inline:auto;">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#C8B89A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin:0 auto 24px;">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/>
                    </svg>
                    <h3 class="hv-card__title" style="margin-inline:auto;">Your request list is empty</h3>
                    <p class="hv-card__text" style="margin-inline:auto;">View Leivant project resources and add the materials, tools, or equipment your site needs reviewed.</p>
                    <a href="{{ route('products.index') }}" class="hv-btn" style="margin:24px auto 0;">View Resources</a>
                </div>
            @else
                <div class="hv-grid hv-grid--2">
                    <div style="display:grid;gap:14px;">
                        @foreach ($items as $item)
                            @php($lineTotal = $item['line_total'] ?? ($item['price'] * $item['quantity'] * max(1, $item['rental_days'] ?? 1)))
                            <article class="hv-card" style="min-height:0;">
                                <img src="{{ $item['image'] ?? asset('images/tools/tool-placeholder.svg') }}" alt="{{ $item['name'] }}" loading="lazy" decoding="async" style="width:72px;height:72px;object-fit:cover;border-radius:3px;">
                                <h3 class="hv-card__title">{{ $item['name'] }}</h3>
                                <p class="hv-card__text">{{ $item['unit'] }} <span class="hv-pill" style="margin-left:6px;">{{ ($item['purchase_type'] ?? 'buy') === 'rent' ? 'Equipment' : 'Material' }}</span></p>
                                <p class="hv-mono hv-amber" style="margin-top:12px;font-size:18px;">TZS {{ number_format($lineTotal) }}</p>
                                @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                    <p class="hv-card__text" style="margin-top:8px;">{{ $item['rental_start_date'] }} to {{ $item['rental_end_date'] }} - {{ $item['rental_days'] ?? 1 }} day(s)</p>
                                @endif
                                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-top:18px;">
                                    <form method="POST" action="{{ route('cart.update', $item['slug']) }}" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="purchase_type" value="{{ $item['purchase_type'] ?? 'buy' }}">
                                        <input type="hidden" name="rental_start_date" value="{{ $item['rental_start_date'] ?? '' }}">
                                        <input type="hidden" name="rental_end_date" value="{{ $item['rental_end_date'] ?? '' }}">
                                        <input type="number" min="1" max="999" name="quantity" value="{{ $item['quantity'] }}" aria-label="Quantity for {{ $item['name'] }}" style="width:84px;min-height:44px;">
                                        <button type="submit" class="hv-btn" style="min-height:44px;">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $item['slug']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="purchase_type" value="{{ $item['purchase_type'] ?? 'buy' }}">
                                        <input type="hidden" name="rental_start_date" value="{{ $item['rental_start_date'] ?? '' }}">
                                        <input type="hidden" name="rental_end_date" value="{{ $item['rental_end_date'] ?? '' }}">
                                        <button type="submit" class="hv-btn hv-btn--ghost" style="min-height:44px;">Remove</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <aside class="hv-card" style="min-height:0;">
                        <h3 class="hv-card__title">Request summary</h3>
                        <table class="hv-table hv-mt">
                            <tr><th>Items</th><td>{{ $items->sum('quantity') }}</td></tr>
                            <tr><th>Subtotal</th><td>TZS {{ number_format($total) }}</td></tr>
                            <tr><th>Delivery</th><td>Reviewed by Leivant</td></tr>
                        </table>
                        <div class="hv-divider hv-mt"></div>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:18px;">
                            <span class="hv-mono hv-muted" style="font-size:11px;">Indicative value</span>
                            <strong class="hv-amber" style="font-family:var(--hv-display);font-size:30px;">TZS {{ number_format($total) }}</strong>
                        </div>
                        <p class="hv-card__text" style="margin-top:20px;">After submission, Leivant confirms availability, delivery needs, site suitability, and payment instructions directly by phone or WhatsApp.</p>
                        <a href="{{ route('checkout.index') }}" class="hv-btn" style="width:100%;margin-top:20px;">Send Request to Leivant</a>
                        <a href="{{ route('products.index') }}" class="hv-btn hv-btn--ghost" style="width:100%;margin-top:12px;">Continue Resources</a>
                    </aside>
                </div>
            @endif
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
        <h2 class="hv-cta__title">Need help choosing resources?</h2>
        <p class="hv-cta__text">Send your site requirements and the Leivant Construction Desk will review availability, delivery, and project fit before you submit a request.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
            <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
            <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
            <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
        </div>
        <a href="{{ route('contact.index') }}" class="hv-btn">Contact Leivant</a>
    </section>
</div>
@endsection
