@extends('layouts.seo')

@section('title', 'Send Request | Leivant Project Resources')
@section('meta_description', 'Submit construction resource request details to Leivant for availability, site-fit, delivery, and timing review.')
@section('canonical', route('checkout.index'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="cm-page">
    <section class="proj-hero" aria-label="Send request">
        <img src="{{ asset('leivant-webp/20260523_142200.jpg.webp') }}" alt="" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);">Request review</p>
            <h1>Send resource details <span class="amber">for Leivant review.</span></h1>
            <p class="ed-lead">Leivant will review availability, rental dates, delivery requirements, site suitability, and the right next step before any payment is arranged.</p>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-wrap cm-split">
            <div class="form-card">
                <p class="ed-label">Customer details</p>
                <h2 style="font-size:26px;margin-top:10px;">Resource review request</h2>
                <p class="cm-muted" style="line-height:1.7;margin-top:8px;">Submit the request and Leivant will confirm availability, site suitability, delivery details, and payment instructions directly with you.</p>

                @if ($errors->any())
                    <div class="cm-note danger" style="margin-top:18px;">
                        <strong>Please check the checkout details.</strong>
                        <ul style="margin:8px 0 0;padding-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('checkout.store') }}" class="form-grid" style="margin-top:22px;">
                    @csrf
                    <label>
                        <span>Full name</span>
                        <input name="name" value="{{ old('name') }}" required>
                    </label>
                    <label>
                        <span>Phone</span>
                        <input name="phone" value="{{ old('phone') }}" placeholder="+255..." required>
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}">
                    </label>
                    <label>
                        <span>Region</span>
                        <select name="region" required>
                            @foreach ($regions as $region)
                                <option value="{{ $region }}" @selected(old('region', 'Dar es Salaam') === $region)>{{ $region }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="full">
                        <span>Project / site location</span>
                        <textarea name="delivery_address" required>{{ old('delivery_address') }}</textarea>
                    </label>
                    <label class="full">
                        <span>Project / request notes</span>
                        <textarea name="delivery_notes" placeholder="Site requirement, supervisor contact, rental timing, access notes, or item condition questions.">{{ old('delivery_notes') }}</textarea>
                    </label>
                    <fieldset class="full" style="border:0;padding:0;margin:0;">
                        <legend style="margin-bottom:10px;font-family:var(--font-mono);font-size:11px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:#221d18;">Preferred payment method</legend>
                        <div class="cm-paygrid">
                            @foreach ([
                                'mpesa' => 'M-Pesa',
                                'tigopesa' => 'Tigo Pesa',
                                'airtel' => 'Airtel Money',
                                'halopesa' => 'Halopesa',
                            ] as $value => $label)
                                <label class="cm-pay">
                                    <input type="radio" name="payment_channel" value="{{ $value }}" @checked(old('payment_channel', 'mpesa') === $value)>
                                    <strong>{{ $label }}</strong>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <div class="cm-note full">No online payment is charged here. Leivant reviews the request first, then shares availability, delivery details, and payment instructions by phone or WhatsApp.</div>
                    <button class="cm-btn full" style="grid-column:1/-1;" type="submit" @disabled($items->isEmpty())>Send Request</button>
                </form>
            </div>

            <aside class="form-card">
                <p class="ed-label">Request summary</p>
                <h2 style="font-size:32px;margin-top:10px;">TZS {{ number_format($total) }}</h2>
                <div style="display:grid;gap:12px;margin-top:22px;">
                    @forelse ($items as $item)
                        <article class="cm-line">
                            <div>
                                <span class="cm-tag">{{ ($item['purchase_type'] ?? 'buy') === 'rent' ? 'Equipment' : 'Material' }}</span>
                                <h3 style="margin-top:8px;">{{ $item['name'] }}</h3>
                                <p class="cm-muted" style="font-size:13px;margin:4px 0 0;">
                                    {{ $item['quantity'] }} x TZS {{ number_format($item['price']) }}
                                    @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                        x {{ $item['rental_days'] ?? 1 }} day(s)
                                    @endif
                                </p>
                                @if (($item['purchase_type'] ?? 'buy') === 'rent')
                                    <p class="cm-muted" style="font-size:13px;margin:0;">{{ $item['rental_start_date'] }} to {{ $item['rental_end_date'] }}</p>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="cm-note danger">No items in the request list.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>
</div>
@endsection
