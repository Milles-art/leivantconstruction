@extends('layouts.seo')

@section('title', 'Order Confirmation | Leivant Marketplace')
@section('meta_description', 'Leivant marketplace order confirmation and next steps for construction equipment, tools, and materials.')
@section('robots', 'noindex, nofollow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
<div class="hv-page">
    @php
        $hasRentalItems = collect($order['items'] ?? [])->contains(fn ($item) => ($item['purchase_type'] ?? 'buy') === 'rent');
        $confirmationMessage = $hasRentalItems
            ? 'Leivant has received your tools and equipment request. The Construction Desk will confirm availability, rental dates, payment instructions, and site requirements using your submitted contact details.'
            : 'Leivant has received your tools and materials request. The Construction Desk will confirm product availability, payment instructions, and site requirements using your submitted contact details.';
    @endphp

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Order confirmation">
        <div class="hv-hero__media">
            <img src="{{ asset('leivant-webp/20260523_142200.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        </div>
        <div class="hv-hero__inner">
            <p class="hv-eyebrow">Order confirmed</p>
            <h1 class="hv-hero__title" style="font-size:clamp(38px,6vw,84px);word-break:break-all;">{{ $order['number'] }}</h1>
            <p class="hv-hero__lead">{{ $confirmationMessage }}</p>
            <div class="hv-hero__actions">
                <a href="{{ route('products.index') }}" class="hv-btn">Continue Marketplace</a>
                <a href="{{ route('contact.index') }}" class="hv-btn hv-btn--ghost">Contact Leivant</a>
            </div>
        </div>
    </section>

    {{-- NEXT STEPS --}}
    <section class="hv-section hv-section--tight" aria-label="Next steps">
        <div class="hv-wrap">
            <div class="hv-head">
                <p class="hv-eyebrow">What happens next</p>
                <h2 class="hv-title">Your request is with the Construction Desk</h2>
            </div>
            <div class="hv-grid hv-grid--3">
                @foreach ([
                    'Leivant reviews item availability and the submitted site details.',
                    'The Construction Desk contacts the customer using the submitted phone or email.',
                    'Payment instructions, item readiness, and site notes are finalized.',
                ] as $i => $step)
                    <article class="hv-card">
                        <span class="hv-card__num">{{ sprintf('%02d', $i + 1) }}</span>
                        <p class="hv-card__text" style="margin-top:auto;">{{ $step }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ORDER DETAILS --}}
    <section class="hv-section hv-section--surface" aria-label="Order details">
        <div class="hv-wrap">
            <div class="hv-split">
                <div class="hv-card" style="min-height:0;">
                    <p class="hv-eyebrow">Items</p>
                    <h3 class="hv-card__title">Order lines</h3>
                    <div style="display:grid;gap:12px;margin-top:22px;">
                        @foreach ($order['items'] as $item)
                            <article style="display:flex;gap:16px;padding-bottom:14px;border-bottom:1px solid var(--hv-line);">
                                <img src="{{ $item['image'] ?? asset('images/tools/tool-placeholder.svg') }}" alt="{{ $item['name'] }}" loading="lazy" decoding="async" style="width:72px;height:72px;object-fit:cover;border-radius:3px;">
                                <div>
                                    <span class="hv-pill">{{ ($item['purchase_type'] ?? 'buy') === 'rent' ? 'Rental' : 'Purchase' }}</span>
                                    <h4 class="hv-card__title" style="font-size:20px;margin-top:8px;">{{ $item['name'] }}</h4>
                                    <p class="hv-card__text" style="font-size:13px;margin-top:4px;">
                                        {{ $item['quantity'] }} x TZS {{ number_format($item['price']) }} / {{ $item['unit'] }}
                                        @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                            x {{ $item['rental_days'] ?? 1 }} day(s)
                                        @endif
                                    </p>
                                    @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                        <p class="hv-card__text" style="font-size:13px;">{{ $item['rental_start_date'] }} to {{ $item['rental_end_date'] }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <aside class="hv-card" style="min-height:0;">
                    <p class="hv-eyebrow">Customer</p>
                    <h3 class="hv-card__title" style="font-size:clamp(32px,3.5vw,48px);">TZS {{ number_format($order['total']) }}</h3>
                    <table class="hv-table hv-mt">
                        <tr><th>Name:</th><td>{{ $order['customer']['name'] }}</td></tr>
                        <tr><th>Phone:</th><td>{{ $order['customer']['phone'] }}</td></tr>
                        <tr><th>Region:</th><td>{{ $order['customer']['region'] }}</td></tr>
                        <tr><th>Site:</th><td>{{ $order['customer']['delivery_address'] ?? 'Provided at checkout' }}</td></tr>
                        <tr><th>Preferred payment:</th><td>{{ str($order['customer']['payment_channel'])->headline() }}</td></tr>
                        <tr><th>Status:</th><td>{{ ucfirst($order['status']) }}</td></tr>
                    </table>
                </aside>
            </div>
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
        <h2 class="hv-cta__title">Need to follow up?</h2>
        <p class="hv-cta__text">Contact the Leivant Construction Desk with your order number for availability, payment instructions, and site requirements.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
            <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
            <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
            <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
        </div>
        <a href="{{ route('contact.index') }}" class="hv-btn">Contact Leivant</a>
    </section>
</div>
@endsection
