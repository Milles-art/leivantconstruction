@extends('layouts.seo')

@section('title', 'Order Confirmation | Leivant Marketplace')
@section('meta_description', 'Leivant marketplace order confirmation and next steps for construction equipment, tools, and materials.')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="cm-page">
    @php
        $hasRentalItems = collect($order['items'] ?? [])->contains(fn ($item) => ($item['purchase_type'] ?? 'buy') === 'rent');
        $confirmationMessage = $hasRentalItems
            ? 'Leivant has received your tools and equipment request. The Construction Desk will confirm availability, rental dates, payment instructions, and site requirements using your submitted contact details.'
            : 'Leivant has received your tools and materials request. The Construction Desk will confirm product availability, payment instructions, and site requirements using your submitted contact details.';
    @endphp

    <section class="proj-hero" aria-label="Order confirmation">
        <img src="{{ asset('leivant-webp/20260523_142200.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);">Order confirmed</p>
            <h1 style="font-size:clamp(28px,4.5vw,54px);word-break:break-all;">{{ $order['number'] }}</h1>
            <p class="ed-lead">{{ $confirmationMessage }}</p>
            <div class="ed-hero-cta">
                <a href="{{ route('products.index') }}" class="ed-btn ed-btn-solid">Continue Marketplace</a>
                <a href="{{ route('contact.index') }}" class="ed-btn ed-btn-ghost">Contact Leivant</a>
            </div>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-wrap">
            <div class="cm-steps">
                @foreach ([
                    'Leivant reviews item availability and the submitted site details.',
                    'The Construction Desk contacts the customer using the submitted phone or email.',
                    'Payment instructions, item readiness, and site notes are finalized.',
                ] as $i => $step)
                    <div class="cm-step">
                        <span class="n">{{ sprintf('%02d', $i + 1) }}</span>
                        <p>{{ $step }}</p>
                    </div>
                @endforeach
            </div>

            <div class="cm-split" style="margin-top:44px;">
                <div class="form-card">
                    <p class="ed-label">Items</p>
                    <h2 style="font-size:26px;margin-top:10px;">Order lines</h2>
                    <div style="display:grid;gap:12px;margin-top:22px;">
                        @foreach ($order['items'] as $item)
                            <article class="cm-line">
                                <img src="{{ $item['image'] ?? asset('images/tools/tool-placeholder.svg') }}" alt="{{ $item['name'] }}" loading="lazy" decoding="async">
                                <div>
                                    <span class="cm-tag">{{ ($item['purchase_type'] ?? 'buy') === 'rent' ? 'Rental' : 'Purchase' }}</span>
                                    <h3 style="margin-top:8px;">{{ $item['name'] }}</h3>
                                    <p class="cm-muted" style="font-size:13px;margin:4px 0 0;">
                                        {{ $item['quantity'] }} x TZS {{ number_format($item['price']) }} / {{ $item['unit'] }}
                                        @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                            x {{ $item['rental_days'] ?? 1 }} day(s)
                                        @endif
                                    </p>
                                    @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                        <p class="cm-muted" style="font-size:13px;margin:0;">{{ $item['rental_start_date'] }} to {{ $item['rental_end_date'] }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <aside class="form-card">
                    <p class="ed-label">Customer</p>
                    <h2 style="font-size:32px;margin-top:10px;">TZS {{ number_format($order['total']) }}</h2>
                    <div style="display:grid;gap:12px;margin-top:22px;font-size:14px;">
                        <p><strong>Name:</strong> {{ $order['customer']['name'] }}</p>
                        <p><strong>Phone:</strong> {{ $order['customer']['phone'] }}</p>
                        <p><strong>Region:</strong> {{ $order['customer']['region'] }}</p>
                        <p><strong>Site:</strong> {{ $order['customer']['delivery_address'] ?? 'Provided at checkout' }}</p>
                        <p><strong>Preferred payment:</strong> {{ str($order['customer']['payment_channel'])->headline() }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($order['status']) }}</p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</div>
@endsection
