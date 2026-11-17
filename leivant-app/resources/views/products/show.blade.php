@extends('layouts.seo')

@section('title', $product['name'].' | Leivant Project Resources')
@section('meta_description', $product['summary'] ?? 'Construction tools, equipment, and materials reviewed through Leivant project resources in Tanzania.')
@section('canonical', route('products.show', $product['slug']))
@section('og_image', $product['image'])

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
  @php
    $rentalOnly = ($product['is_for_rent'] ?? false) && ! ($product['is_for_sale'] ?? true);
    $rentable = $rentalOnly
        && ($product['is_for_rent'] ?? false)
        && ! empty($product['rental_price_per_day']);
    $gallery = collect($product['gallery'] ?? [$product['image']])->filter()->values();
    $tomorrow = now()->addDay()->toDateString();
    $primaryOfferPrice = $rentable
        ? (int) ($product['rental_price_per_day'] ?? 0)
        : (int) ($product['price'] ?? 0);
    $availability = ((int) ($product['stock'] ?? 0) > 0 && ($product['availability_status'] ?? 'available') !== 'booked')
        ? 'https://schema.org/InStock'
        : 'https://schema.org/LimitedAvailability';
    $imageUrls = $gallery
        ->map(fn ($image) => preg_match('#^https?://#i', (string) $image) ? $image : url($image))
        ->values()
        ->all();
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product['name'],
        'description' => $product['summary'],
        'image' => $imageUrls,
        'sku' => $product['slug'],
        'brand' => [
            '@type' => 'Brand',
            'name' => 'Leivant',
        ],
        'category' => $product['category'],
        'offers' => [
            '@type' => 'Offer',
            'url' => route('products.show', $product['slug']),
            'priceCurrency' => 'TZS',
            'price' => (string) max(1, $primaryOfferPrice),
            'availability' => $availability,
            'itemCondition' => 'https://schema.org/NewCondition',
            'priceValidUntil' => now()->addYear()->toDateString(),
            'seller' => [
                '@type' => 'Organization',
                'name' => 'Leivant Construction Solutions',
            ],
            'priceSpecification' => [
                '@type' => 'UnitPriceSpecification',
                'priceCurrency' => 'TZS',
                'price' => (string) max(1, $primaryOfferPrice),
                'unitText' => $rentable ? 'DAY' : strtoupper($product['unit'] ?? 'ITEM'),
            ],
        ],
    ];
  @endphp
  <script type="application/ld+json">
{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
  </script>

  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Resource overview">
      <div class="hv-hero__media">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">{{ $product['category'] }}</p>
        <h1 class="hv-hero__title">{{ $product['name'] }}</h1>
        <p class="hv-hero__lead">{{ $product['summary'] }}</p>
        <div class="hv-hero__actions">
          <a href="#request" class="hv-btn">Add to request list</a>
          <a href="{{ route('products.index') }}" class="hv-btn hv-btn--ghost">← Resources</a>
        </div>
      </div>
    </section>

    {{-- PRODUCT DETAIL --}}
    <section class="hv-section hv-section--surface" aria-label="Product details">
      <div class="hv-wrap hv-split">
        <div>
          <div class="hv-split__media" style="margin-bottom:10px;">
            <img id="cm-main-image" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" width="900" height="650" loading="eager" decoding="async" fetchpriority="high">
          </div>
          @if ($gallery->count() > 1)
            <div class="hv-grid hv-grid--4">
              @foreach ($gallery as $image)
                <button type="button" data-gallery-thumb data-src="{{ $image }}" @class(['is-active' => $loop->first]) aria-label="Show image {{ $loop->iteration }}" style="padding:0;border:2px solid transparent;background:transparent;cursor:pointer;overflow:hidden;border-radius:3px;opacity:.78;">
                  <img src="{{ $image }}" alt="{{ $product['name'] }} image {{ $loop->iteration }}" width="220" height="150" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover;aspect-ratio:1 / 1;">
                </button>
              @endforeach
            </div>
          @endif
        </div>

        <div id="request">
          <p class="hv-eyebrow">Leivant resource</p>
          <h2 class="hv-title" style="font-size:clamp(36px,4.4vw,64px);margin-top:16px;">{{ $product['name'] }}</h2>
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:16px;">
            <span class="hv-pill">{{ $product['category'] }}</span>
            @if ($rentable)
              <span class="hv-pill">TZS {{ number_format($product['rental_price_per_day']) }} / day</span>
            @elseif ($rentalOnly)
              <span class="hv-pill">Ask Leivant</span>
            @else
              <span class="hv-pill">TZS {{ number_format($product['price']) }} / {{ $product['unit'] }}</span>
            @endif
          </div>
          <p class="hv-lead" style="margin-top:20px;">{{ $product['summary'] }}</p>

          <table class="hv-table hv-mt" style="margin-top:24px;">
            <thead>
              <tr>
                <th>Detail</th>
                <th>Leivant resource specification</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="hv-mono">Category</td>
                <td>{{ $product['category'] }}</td>
              </tr>
              <tr>
                <td class="hv-mono">Price</td>
                <td class="hv-amber">
                  @if ($rentable)
                    TZS {{ number_format($product['rental_price_per_day']) }} per day
                  @elseif ($rentalOnly)
                    Ask Leivant
                  @else
                    TZS {{ number_format($product['price']) }} per {{ $product['unit'] }}
                  @endif
                </td>
              </tr>
              <tr>
                <td class="hv-mono">Reference</td>
                <td>{{ $product['slug'] }}</td>
              </tr>
              <tr>
                <td class="hv-mono">Availability</td>
                <td>{{ $product['availability_status'] ?? 'available' }}</td>
              </tr>
            </tbody>
          </table>

          <div class="hv-mt" style="margin-top:26px;">
            @unless ($rentalOnly)
              <form method="POST" action="{{ route('cart.store') }}" class="market-add-to-cart hv-form">
                @csrf
                <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                <input type="hidden" name="name" value="{{ $product['name'] }}">
                <input type="hidden" name="price" value="{{ $product['price'] }}">
                <input type="hidden" name="unit" value="{{ $product['unit'] }}">
                <input type="hidden" name="image" value="{{ $product['image'] }}">
                <input type="hidden" name="purchase_type" value="buy">
                <label class="hv-field">
                  <span>Quantity</span>
                  <input type="number" min="1" max="999" name="quantity" value="1">
                </label>
                <button class="hv-btn" type="submit" data-default-label="Add to Request List">Add to Request List</button>
              </form>
            @endunless

            @if ($rentable)
              <form method="POST" action="{{ route('cart.store') }}" class="market-add-to-cart hv-form" style="margin-top:16px;">
                @csrf
                <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                <input type="hidden" name="name" value="{{ $product['name'] }}">
                <input type="hidden" name="price" value="{{ $product['rental_price_per_day'] }}">
                <input type="hidden" name="unit" value="day">
                <input type="hidden" name="image" value="{{ $product['image'] }}">
                <input type="hidden" name="purchase_type" value="rent">
                <div class="hv-form__row">
                  <label class="hv-field">
                    <span>Units</span>
                    <input type="number" min="1" max="999" name="quantity" value="1">
                  </label>
                  <label class="hv-field">
                    <span>Start</span>
                    <input type="date" name="rental_start_date" min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required>
                  </label>
                </div>
                <label class="hv-field">
                  <span>Return</span>
                  <input type="date" name="rental_end_date" min="{{ now()->toDateString() }}" value="{{ $tomorrow }}" required>
                </label>
                <button class="hv-btn" type="submit" data-default-label="Request Equipment">Request Equipment</button>
              </form>
            @elseif ($rentalOnly)
              <a class="hv-btn hv-btn--ghost" href="{{ route('contact.index') }}">Ask Availability</a>
            @endif
            <p class="hv-muted hv-mono" style="margin-top:14px;font-size:12px;">Prices are indicative. Leivant confirms availability and delivery.</p>
          </div>
        </div>
      </div>
    </section>

    @if (count($relatedProducts))
      {{-- RELATED RESOURCES --}}
      <section class="hv-section" aria-label="Related resources">
        <div class="hv-wrap">
          <div class="hv-head">
            <p class="hv-eyebrow">Keep building</p>
            <h2 class="hv-title">Related</h2>
          </div>
          <div class="hv-grid hv-grid--4">
            @foreach ($relatedProducts as $relatedProduct)
              @php
                $relatedRentalOnly = ($relatedProduct['is_for_rent'] ?? false) && ! ($relatedProduct['is_for_sale'] ?? true);
                $relatedRentable = $relatedRentalOnly
                    && ($relatedProduct['is_for_rent'] ?? false)
                    && ! empty($relatedProduct['rental_price_per_day']);
                $relatedToday = now()->toDateString();
                $relatedTomorrow = now()->addDay()->toDateString();
              @endphp
              <article class="hv-photo-card product-click-add" role="button" tabindex="0" aria-label="Add {{ $relatedProduct['name'] }} to request list" style="min-height:420px;">
                <img src="{{ $relatedProduct['image'] }}" alt="{{ $relatedProduct['name'] }}" width="900" height="650" loading="lazy" decoding="async">
                <span class="hv-photo-card__num">{{ sprintf('%02d', $loop->iteration) }}</span>
                <h3 class="hv-photo-card__title">{{ $relatedProduct['name'] }}</h3>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                  @if ($relatedRentable)
                    <span class="hv-pill">TZS {{ number_format($relatedProduct['rental_price_per_day']) }} / day</span>
                  @elseif ($relatedRentalOnly)
                    <span class="hv-pill">Ask Leivant</span>
                  @else
                    <span class="hv-pill">TZS {{ number_format($relatedProduct['price']) }} / {{ $relatedProduct['unit'] }}</span>
                  @endif
                </div>

                @if ($relatedRentable)
                  <form method="POST" action="{{ route('cart.store') }}" class="market-add-to-cart" style="margin-top:14px;">
                    @csrf
                    <input type="hidden" name="slug" value="{{ $relatedProduct['slug'] }}">
                    <input type="hidden" name="name" value="{{ $relatedProduct['name'] }}">
                    <input type="hidden" name="price" value="{{ $relatedProduct['rental_price_per_day'] }}">
                    <input type="hidden" name="unit" value="day">
                    <input type="hidden" name="image" value="{{ $relatedProduct['image'] }}">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="purchase_type" value="rent">
                    <input type="hidden" name="rental_start_date" value="{{ $relatedToday }}">
                    <input type="hidden" name="rental_end_date" value="{{ $relatedTomorrow }}">
                    <button class="hv-btn" type="submit" data-default-label="Request Equipment" style="width:100%;margin-top:12px;">Request Equipment</button>
                  </form>
                @elseif ($relatedRentalOnly)
                  <a class="hv-btn hv-btn--ghost" href="{{ route('contact.index') }}" style="width:100%;margin-top:14px;">Ask Leivant</a>
                @else
                  <form method="POST" action="{{ route('cart.store') }}" class="market-add-to-cart" style="margin-top:14px;">
                    @csrf
                    <input type="hidden" name="slug" value="{{ $relatedProduct['slug'] }}">
                    <input type="hidden" name="name" value="{{ $relatedProduct['name'] }}">
                    <input type="hidden" name="price" value="{{ $relatedProduct['price'] }}">
                    <input type="hidden" name="unit" value="{{ $relatedProduct['unit'] }}">
                    <input type="hidden" name="image" value="{{ $relatedProduct['image'] }}">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="purchase_type" value="buy">
                    <button class="hv-btn" type="submit" data-default-label="Add to List" style="width:100%;margin-top:12px;">Add to List</button>
                  </form>
                @endif
              </article>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
      <h2 class="hv-cta__title">Need this resource on site?</h2>
      <p class="hv-cta__text">Send your requirement and Leivant will confirm availability, delivery timing, and the practical next step for your project.</p>
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
    (function () {
        var main = document.getElementById('cm-main-image');
        var thumbs = document.querySelectorAll('[data-gallery-thumb]');
        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                if (main) main.src = thumb.getAttribute('data-src');
                thumbs.forEach(function (t) { t.classList.remove('is-active'); });
                thumb.classList.add('is-active');
            });
        });
    })();

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
            if (button) { button.disabled = true; button.textContent = 'Adding...'; }
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
                if (!response.ok) throw new Error(data.message || 'Unable to add item.');
                document.querySelectorAll('[data-cart-count]').forEach(function (node) { node.textContent = data.cart_count; });
                document.querySelectorAll('[data-cart-total]').forEach(function (node) { node.textContent = 'TZS ' + new Intl.NumberFormat('en-US').format(data.cart_total || 0); });
                if (button) { button.textContent = 'Added to List'; setTimeout(function () { button.textContent = defaultLabel; }, 1200); }
            } catch (error) {
                if (button) { button.textContent = error.message || 'Try again'; setTimeout(function () { button.textContent = defaultLabel; }, 2200); }
            } finally {
                if (button) { button.disabled = false; }
                window.LeivantLoading?.hide?.();
            }
        });
    });
  </script>
@endpush
