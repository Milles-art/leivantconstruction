@extends('layouts.seo')

@section('title', 'Send Request | Leivant Project Resources')
@section('meta_description', 'Submit construction resource request details to Leivant for availability, site-fit, delivery, and timing review.')
@section('canonical', route('checkout.index'))
@section('robots', 'noindex, nofollow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
<div class="hv-page">
    {{-- HERO --}}
    <section class="hv-hero" aria-label="Send request">
        <div class="hv-hero__media">
            <img src="{{ asset('leivant-webp/20260523_142200.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        </div>
        <div class="hv-hero__inner">
            <p class="hv-eyebrow">Request review</p>
            <h1 class="hv-hero__title">Send resource details <span class="hv-amber">for Leivant review.</span></h1>
            <p class="hv-hero__lead">Leivant will review availability, rental dates, delivery requirements, site suitability, and the right next step before any payment is arranged.</p>
            <div class="hv-hero__actions">
                <a href="{{ route('cart.index') }}" class="hv-btn hv-btn--ghost">Back to request list</a>
            </div>
        </div>
    </section>

    {{-- CHECKOUT --}}
    <section class="hv-section hv-section--surface" aria-label="Checkout details">
        <div class="hv-wrap">
            <div class="hv-head">
                <p class="hv-eyebrow">Customer details</p>
                <h2 class="hv-title">Resource review request</h2>
                <p class="hv-lead">Submit the request and Leivant will confirm availability, site suitability, delivery details, and payment instructions directly with you.</p>
            </div>

            @if ($errors->any())
                <div class="hv-card" style="min-height:0;border-color:#ef7b6b;margin-bottom:28px;">
                    <strong class="hv-amber">Please check the checkout details.</strong>
                    <ul class="hv-list hv-mt" style="margin-top:8px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="hv-split">
                <div class="hv-card" style="min-height:0;">
                    <p class="hv-eyebrow">Customer details</p>
                    <h3 class="hv-card__title">Resource review request</h3>
                    <p class="hv-card__text" style="margin-top:8px;">Submit the request and Leivant will confirm availability, site suitability, delivery details, and payment instructions directly with you.</p>

                    <form method="POST" action="{{ route('checkout.store') }}" class="hv-form" style="margin-top:22px;">
                        @csrf
                        <div class="hv-form__row">
                            <label class="hv-field">
                                <span>Full name</span>
                                <input name="name" value="{{ old('name') }}" required>
                            </label>
                            <label class="hv-field">
                                <span>Phone</span>
                                <input name="phone" value="{{ old('phone') }}" placeholder="+255..." required>
                            </label>
                        </div>
                        <div class="hv-form__row">
                            <label class="hv-field">
                                <span>Email</span>
                                <input type="email" name="email" value="{{ old('email') }}">
                            </label>
                            <label class="hv-field">
                                <span>Region</span>
                                <select name="region" required>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region }}" @selected(old('region', 'Dar es Salaam') === $region)>{{ $region }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <label class="hv-field">
                            <span>Project / site location</span>
                            <textarea name="delivery_address" required>{{ old('delivery_address') }}</textarea>
                        </label>
                        <label class="hv-field">
                            <span>Project / request notes</span>
                            <textarea name="delivery_notes" placeholder="Site requirement, supervisor contact, rental timing, access notes, or item condition questions.">{{ old('delivery_notes') }}</textarea>
                        </label>
                        <fieldset style="border:0;padding:0;margin:0;">
                            <legend class="hv-eyebrow" style="margin-bottom:14px;">Preferred payment method</legend>
                            <div style="display:flex;flex-wrap:wrap;gap:10px;">
                                @foreach ([
                                    'mpesa' => 'M-Pesa',
                                    'tigopesa' => 'Tigo Pesa',
                                    'airtel' => 'Airtel Money',
                                    'halopesa' => 'Halopesa',
                                ] as $value => $label)
                                    <label class="hv-pill" style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;">
                                        <input type="radio" name="payment_channel" value="{{ $value }}" @checked(old('payment_channel', 'mpesa') === $value) style="width:auto;min-height:0;">
                                        <strong>{{ $label }}</strong>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                        <p class="hv-card__text">No online payment is charged here. Leivant reviews the request first, then shares availability, delivery details, and payment instructions by phone or WhatsApp.</p>
                        <button class="hv-btn" style="width:100%;" type="submit" @disabled($items->isEmpty())>Send Request</button>
                    </form>
                </div>

                <aside class="hv-card" style="min-height:0;">
                    <p class="hv-eyebrow">Request summary</p>
                    <h3 class="hv-card__title" style="font-size:clamp(32px,3.5vw,48px);">TZS {{ number_format($total) }}</h3>
                    <div style="display:grid;gap:12px;margin-top:22px;">
                        @forelse ($items as $item)
                            <article style="padding-bottom:12px;border-bottom:1px solid var(--hv-line);">
                                <span class="hv-pill">{{ ($item['purchase_type'] ?? 'buy') === 'rent' ? 'Equipment' : 'Material' }}</span>
                                <h4 class="hv-card__title" style="font-size:20px;margin-top:8px;">{{ $item['name'] }}</h4>
                                <p class="hv-card__text" style="font-size:13px;margin-top:4px;">
                                    {{ $item['quantity'] }} x TZS {{ number_format($item['price']) }}
                                    @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                        x {{ $item['rental_days'] ?? 1 }} day(s)
                                    @endif
                                </p>
                                @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                    <p class="hv-card__text" style="font-size:13px;">{{ $item['rental_start_date'] }} to {{ $item['rental_end_date'] }}</p>
                                @endif
                            </article>
                        @empty
                            <p class="hv-card__text" style="color:#ef7b6b;">No items in the request list.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
        <h2 class="hv-cta__title">Questions before you send?</h2>
        <p class="hv-cta__text">The Leivant Construction Desk can confirm availability, rental timing, delivery, and site requirements before you submit a resource request.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
            <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
            <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
            <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
        </div>
        <a href="{{ route('contact.index') }}" class="hv-btn">Contact Leivant</a>
    </section>
</div>
@endsection
