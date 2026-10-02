@props(['product'])

@php
    $rentalOnly = ($product['is_for_rent'] ?? false) && ! ($product['is_for_sale'] ?? true);
    $rentable = $rentalOnly
        && ($product['is_for_rent'] ?? false)
        && ! empty($product['rental_price_per_day']);
    $today = now()->toDateString();
    $tomorrow = now()->addDay()->toDateString();
@endphp

<article {{ $attributes->class(['pc', 'product-click-add']) }} role="button" tabindex="0" aria-label="Add {{ $product['name'] }} to request list">
    <div class="pc-img">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" width="900" height="650" loading="lazy" decoding="async">
    </div>
    <div class="pc-body">
        <h3>{{ $product['name'] }}</h3>
        @if ($rentable)
            <div class="pc-price">
                <strong>TZS {{ number_format($product['rental_price_per_day']) }}</strong>
                <span>/ day</span>
            </div>
        @elseif ($rentalOnly)
            <div class="pc-price">
                <strong>Ask Leivant</strong>
            </div>
        @else
            <div class="pc-price">
                <strong>TZS {{ number_format($product['price']) }}</strong>
                <span>/ {{ $product['unit'] }}</span>
            </div>
        @endif
        @if ($rentable)
            <form method="POST" action="{{ route('cart.store') }}" class="market-add-to-cart">
                @csrf
                <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                <input type="hidden" name="name" value="{{ $product['name'] }}">
                <input type="hidden" name="price" value="{{ $product['rental_price_per_day'] }}">
                <input type="hidden" name="unit" value="day">
                <input type="hidden" name="image" value="{{ $product['image'] }}">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="purchase_type" value="rent">
                <input type="hidden" name="rental_start_date" value="{{ $today }}">
                <input type="hidden" name="rental_end_date" value="{{ $tomorrow }}">
                <button class="pc-btn" data-default-label="Request Equipment" type="submit">Request Equipment</button>
            </form>
        @elseif ($rentalOnly)
            <a class="pc-btn" href="{{ route('contact.index') }}">Ask Leivant</a>
        @else
            <form method="POST" action="{{ route('cart.store') }}" class="market-add-to-cart">
                @csrf
                <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                <input type="hidden" name="name" value="{{ $product['name'] }}">
                <input type="hidden" name="price" value="{{ $product['price'] }}">
                <input type="hidden" name="unit" value="{{ $product['unit'] }}">
                <input type="hidden" name="image" value="{{ $product['image'] }}">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="purchase_type" value="buy">
                <button class="pc-btn" data-default-label="Add to List" type="submit">Add to List</button>
            </form>
        @endif
    </div>
</article>

@once
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
        </script>
    @endpush
@endonce