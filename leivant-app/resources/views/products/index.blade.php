@extends('layouts.seo')

@section('title', 'Leivant Project Resources | Materials and Equipment Support')
@section('meta_description', 'Review construction materials, tools, and equipment support through Leivant before site delivery, rental timing, or project resource planning is confirmed.')
@section('canonical', route('products.index'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
  @php
    $cartItems = collect(session('cart.items', []))->values();
    $cartCount = $globalCartCount ?? $cartItems->sum(fn ($item) => (int) ($item['quantity'] ?? 1));
    $cartTotal = $cartItems->sum(fn ($item) => (int) ($item['line_total'] ?? 0));
    $mode = request('mode');
  @endphp

  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Project resources introduction">
      <div class="hv-hero__media">
        <img src="{{ asset('images/site/materials-supply.jpg') }}" alt="Construction materials and equipment supplied through Leivant in Tanzania" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">Leivant project resources</p>
        <h1 class="hv-hero__title">Materials &amp; <span class="hv-amber">equipment</span> for the build.</h1>
        <p class="hv-hero__lead">Review construction materials, tools, and equipment support through Leivant before site delivery, rental timing, or project resource planning is confirmed. Build a request list and let our desk verify availability.</p>
        <div class="hv-hero__actions">
          <a href="#catalogue" class="hv-btn">Browse resources</a>
          <a href="{{ route('contact.index') }}" class="hv-btn hv-btn--ghost">Ask Leivant</a>
        </div>
      </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="hv-section hv-section--tight" aria-label="How resources work">
      <div class="hv-wrap">
        <div class="hv-grid hv-grid--3">
          <article class="hv-card">
            <span class="hv-card__num">01</span>
            <h3 class="hv-card__title">Browse materials</h3>
            <p class="hv-card__text">Search cement, sand, blocks, steel, tools, and finishing items for purchase through Leivant.</p>
          </article>
          <article class="hv-card">
            <span class="hv-card__num">02</span>
            <h3 class="hv-card__title">Request equipment</h3>
            <p class="hv-card__text">Switch to equipment to review daily rental options, dates, and site delivery requirements.</p>
          </article>
          <article class="hv-card">
            <span class="hv-card__num">03</span>
            <h3 class="hv-card__title">Confirm with Leivant</h3>
            <p class="hv-card__text">Prices are indicative. Our construction desk confirms availability, timing, and the final request.</p>
          </article>
        </div>
      </div>
    </section>

    {{-- CATALOGUE --}}
    <section id="catalogue" class="hv-section hv-section--surface" style="scroll-margin-top:120px;" aria-label="Catalogue">
      <div class="hv-wrap">
        <div class="hv-head">
          <p class="hv-eyebrow">Resource catalogue</p>
          <h2 class="hv-title">Materials &amp; equipment</h2>
          <p class="hv-lead">Choose a resource type, search the catalogue, and add what you need to your request list.</p>
        </div>

        <form method="GET" action="{{ route('products.index') }}" class="hv-form" data-reveal>
          <div class="hv-form__row">
            <label class="hv-field">
              <span>Search resources</span>
              <input type="search" name="search" value="{{ request('search') }}" placeholder="Search materials, tools, or equipment…" aria-label="Search resources">
            </label>
            <div class="hv-field">
              <span>Choose resource type</span>
              <div class="cm-mode" role="group" aria-label="Choose resource type" style="display:flex;gap:8px;">
                <label @class(['hv-pill' => true, 'is-active' => $mode === 'buy' || ! $mode]) style="cursor:pointer;">
                  <input type="radio" name="mode" value="buy" @checked($mode === 'buy' || ! $mode) hidden>
                  <span>Materials</span>
                </label>
                <label @class(['hv-pill' => true, 'is-active' => $mode === 'rent']) style="cursor:pointer;">
                  <input type="radio" name="mode" value="rent" @checked($mode === 'rent') hidden>
                  <span>Equipment</span>
                </label>
              </div>
            </div>
          </div>
          <div>
            <button class="hv-btn" type="submit">Search</button>
          </div>
        </form>

        <div class="hv-grid hv-grid--4" style="margin-top:32px;">
          @forelse ($products as $product)
            @php
              $cardRentalOnly = ($product['is_for_rent'] ?? false) && ! ($product['is_for_sale'] ?? true);
              $cardRentable = $cardRentalOnly
                  && ($product['is_for_rent'] ?? false)
                  && ! empty($product['rental_price_per_day']);
              $cardToday = now()->toDateString();
              $cardTomorrow = now()->addDay()->toDateString();
            @endphp
            <article class="hv-photo-card product-click-add" role="button" tabindex="0" aria-label="Add {{ $product['name'] }} to request list" style="min-height:420px;">
              <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" width="900" height="650" loading="lazy" decoding="async">
              <span class="hv-photo-card__num">{{ sprintf('%02d', $loop->iteration) }}</span>
              <h3 class="hv-photo-card__title">{{ $product['name'] }}</h3>
              <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                @if ($cardRentable)
                  <span class="hv-pill">TZS {{ number_format($product['rental_price_per_day']) }} / day</span>
                @elseif ($cardRentalOnly)
                  <span class="hv-pill">Ask Leivant</span>
                @else
                  <span class="hv-pill">TZS {{ number_format($product['price']) }} / {{ $product['unit'] }}</span>
                @endif
              </div>

              @if ($cardRentable)
                <form method="POST" action="{{ route('cart.store') }}" class="market-add-to-cart" style="margin-top:14px;">
                  @csrf
                  <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                  <input type="hidden" name="name" value="{{ $product['name'] }}">
                  <input type="hidden" name="price" value="{{ $product['rental_price_per_day'] }}">
                  <input type="hidden" name="unit" value="day">
                  <input type="hidden" name="image" value="{{ $product['image'] }}">
                  <input type="hidden" name="quantity" value="1">
                  <input type="hidden" name="purchase_type" value="rent">
                  <input type="hidden" name="rental_start_date" value="{{ $cardToday }}">
                  <input type="hidden" name="rental_end_date" value="{{ $cardTomorrow }}">
                  <button class="hv-btn" type="submit" data-default-label="Request Equipment" style="width:100%;margin-top:12px;">Request Equipment</button>
                </form>
              @elseif ($cardRentalOnly)
                <a class="hv-btn hv-btn--ghost" href="{{ route('contact.index') }}" style="width:100%;margin-top:14px;">Ask Leivant</a>
              @else
                <form method="POST" action="{{ route('cart.store') }}" class="market-add-to-cart" style="margin-top:14px;">
                  @csrf
                  <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                  <input type="hidden" name="name" value="{{ $product['name'] }}">
                  <input type="hidden" name="price" value="{{ $product['price'] }}">
                  <input type="hidden" name="unit" value="{{ $product['unit'] }}">
                  <input type="hidden" name="image" value="{{ $product['image'] }}">
                  <input type="hidden" name="quantity" value="1">
                  <input type="hidden" name="purchase_type" value="buy">
                  <button class="hv-btn" type="submit" data-default-label="Add to List" style="width:100%;margin-top:12px;">Add to List</button>
                </form>
              @endif
            </article>
          @empty
            <div class="hv-card" style="grid-column:1 / -1;">
              <h3 class="hv-card__title">No matching resources</h3>
              <p class="hv-card__text">Try another search term or switch between materials and equipment.</p>
              <div style="margin-top:22px;">
                <a class="hv-btn hv-btn--ghost" href="{{ route('products.index') }}">Reset filters</a>
              </div>
            </div>
          @endforelse
        </div>

        @if (($totalPages ?? 1) > 1)
          <nav class="cm-pagination" aria-label="Resource pages" style="display:flex;flex-wrap:wrap;align-items:center;gap:14px;margin-top:40px;padding-top:24px;border-top:1px solid var(--hv-line);">
            @if ($currentPage > 1)
              <a class="hv-btn hv-btn--ghost" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">Previous</a>
            @endif
            <span class="hv-muted hv-mono" style="font-size:12px;">Page {{ $currentPage }} of {{ $totalPages }}</span>
            @if ($currentPage < $totalPages)
              <a class="hv-btn" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">Next resources</a>
            @endif
          </nav>
        @endif
      </div>
    </section>

    <a class="market-floating-cart-v2" href="{{ route('cart.index') }}" aria-label="Open request list" style="position:fixed;right:20px;bottom:20px;z-index:40;display:flex;align-items:center;gap:12px;padding:14px 18px;background:var(--hv-amber);color:#0f1211;border-radius:3px;text-decoration:none;font-family:var(--hv-mono);font-size:12px;letter-spacing:.08em;text-transform:uppercase;">
      <x-nav-icon name="cart" />
      <span>Request list</span>
      <strong data-cart-count style="display:inline-grid;place-items:center;min-width:26px;height:26px;padding:0 7px;background:#0f1211;color:#e7bd62;border-radius:999px;font-size:12px;">{{ $cartCount }}</strong>
      <em data-cart-total style="font-style:normal;">TZS {{ number_format($cartTotal) }}</em>
    </a>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
      <h2 class="hv-cta__title">Need help choosing resources?</h2>
      <p class="hv-cta__text">Send your requirement and the Leivant construction desk will review availability, delivery timing, and the practical next step for your site.</p>
      <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
        <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
        <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
        <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
      </div>
      <a href="{{ route('contact.index') }}" class="hv-btn">Send project brief</a>
    </section>

  </div>
@endsection

@push('scripts')
  <script>
    document.querySelectorAll('.product-click-add').forEach(function (card) {
        if (card.dataset.clickAddBound === '1') return;
        card.dataset.clickAddBound = '1';
        function submitCard(event) {
            if (event.type === 'keydown' && ! ['Enter', ' '].includes(event.key)) return;
            if (event.type === 'keydown') event.preventDefault();
            if (event.target.closest('button, a, input, select, textarea, form')) return;
            const form = card.querySelector('.market-add-to-cart');
            if (! form) return;
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }
        }
        card.addEventListener('click', submitCard);
        card.addEventListener('keydown', submitCard);
    });

    document.querySelectorAll('.market-add-to-cart').forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const button = form.querySelector('button[type="submit"]');
            const defaultLabel = button?.dataset.defaultLabel || 'Add to List';
            if (button) {
                button.disabled = true;
                button.textContent = 'Adding...';
            }
            window.LeivantLoading?.show?.();

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: new FormData(form),
                });
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Unable to add item.');
                }
                document.querySelectorAll('[data-cart-count]').forEach(function (node) {
                    node.textContent = data.cart_count;
                });
                document.querySelectorAll('[data-cart-total]').forEach(function (node) {
                    node.textContent = 'TZS ' + new Intl.NumberFormat('en-US').format(data.cart_total || 0);
                });
                if (button) {
                    button.textContent = 'Added to List';
                    setTimeout(function () { button.textContent = defaultLabel; }, 1200);
                }
            } catch (error) {
                if (button) {
                    button.textContent = error.message || 'Try again';
                    setTimeout(function () { button.textContent = defaultLabel; }, 2200);
                }
            } finally {
                if (button) {
                    button.disabled = false;
                }
                window.LeivantLoading?.hide?.();
            }
        });
    });
  </script>
@endpush
