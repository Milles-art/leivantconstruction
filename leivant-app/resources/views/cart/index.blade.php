@extends('layouts.seo')

@section('title', 'Request List | Leivant Project Resources')
@section('meta_description', 'Review selected construction resources before sending details to Leivant for availability, delivery, and site-fit review.')
@section('canonical', route('cart.index'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="cm-page">
    @php($total = $items->sum(fn ($item) => $item['line_total'] ?? ($item['price'] * $item['quantity'] * max(1, $item['rental_days'] ?? 1))))

    <section class="proj-hero" aria-label="Request list">
        <img src="{{ asset('leivant-webp/20260523_142200.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);">Leivant project resources</p>
            <h1>Review your <span class="amber">request list.</span></h1>
            <p class="ed-lead">Once you submit, Leivant reviews the requested materials, tools, or equipment against site location, availability, timing, delivery, and project fit before confirming the next step.</p>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-wrap">
            @if ($items->isEmpty())
                <div class="cm-empty">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#C8B89A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin:0 auto;">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/>
                    </svg>
                    <h2>Your request list is empty</h2>
                    <p>View Leivant project resources and add the materials, tools, or equipment your site needs reviewed.</p>
                    <a href="{{ route('products.index') }}" class="cm-btn" style="text-decoration:none;">View Resources</a>
                </div>
            @else
                <div class="cm-cartlayout">
                    <div style="display:grid;gap:10px;">
                        @foreach ($items as $item)
                            @php($lineTotal = $item['line_total'] ?? ($item['price'] * $item['quantity'] * max(1, $item['rental_days'] ?? 1)))
                            <article class="cm-row">
                                <img src="{{ $item['image'] ?? asset('images/tools/tool-placeholder.svg') }}" alt="{{ $item['name'] }}" loading="lazy" decoding="async">
                                <div class="cm-row-info">
                                    <h3>{{ $item['name'] }}</h3>
                                    <p>{{ $item['unit'] }} <span class="cm-mode-badge {{ ($item['purchase_type'] ?? 'buy') === 'rent' ? 'is-rent' : 'is-buy' }}">{{ ($item['purchase_type'] ?? 'buy') === 'rent' ? 'Equipment' : 'Material' }}</span></p>
                                    <strong>TZS {{ number_format($lineTotal) }}</strong>
                                    @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                        <small>{{ $item['rental_start_date'] }} to {{ $item['rental_end_date'] }} - {{ $item['rental_days'] ?? 1 }} day(s)</small>
                                    @endif
                                </div>
                                <div class="cm-controls">
                                    <form method="POST" action="{{ route('cart.update', $item['slug']) }}" class="cm-qty">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="purchase_type" value="{{ $item['purchase_type'] ?? 'buy' }}">
                                        <input type="hidden" name="rental_start_date" value="{{ $item['rental_start_date'] ?? '' }}">
                                        <input type="hidden" name="rental_end_date" value="{{ $item['rental_end_date'] ?? '' }}">
                                        <input type="number" min="1" max="999" name="quantity" value="{{ $item['quantity'] }}" aria-label="Quantity for {{ $item['name'] }}">
                                        <button type="submit">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $item['slug']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="purchase_type" value="{{ $item['purchase_type'] ?? 'buy' }}">
                                        <input type="hidden" name="rental_start_date" value="{{ $item['rental_start_date'] ?? '' }}">
                                        <input type="hidden" name="rental_end_date" value="{{ $item['rental_end_date'] ?? '' }}">
                                        <button class="cm-remove" type="submit">Remove</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <aside class="cm-summary">
                        <h2>Request summary</h2>
                        <div class="cm-sumrow" style="margin-top:14px;"><span>Items</span><strong>{{ $items->sum('quantity') }}</strong></div>
                        <div class="cm-sumrow"><span>Subtotal</span><strong>TZS {{ number_format($total) }}</strong></div>
                        <div class="cm-sumrow"><span>Delivery</span><em>Reviewed by Leivant</em></div>
                        <div class="cm-sumtotal"><span>Indicative value</span><strong>TZS {{ number_format($total) }}</strong></div>
                        <div class="cm-note" style="margin:14px 0;">After submission, Leivant confirms availability, delivery needs, site suitability, and payment instructions directly by phone or WhatsApp.</div>
                        <a href="{{ route('checkout.index') }}" class="cm-btn" style="width:100%;text-decoration:none;">Send Request to Leivant</a>
                        <a href="{{ route('products.index') }}" class="ed-link" style="display:block;text-align:center;margin-top:14px;">Continue Resources</a>
                    </aside>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
