@extends('layouts.seo')

@section('title', 'Leivant Project Resources | Materials and Equipment Support')
@section('meta_description', 'Review construction materials, tools, and equipment support through Leivant before site delivery, rental timing, or project resource planning is confirmed.')
@section('canonical', route('products.index'))

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap">

<style>
/* ---------- tokens ---------- */
.home-v2,
.home-v2 * { box-sizing: border-box; }

.home-v2 {
    --ink: #151816;
    --ink-2: #1f2421;
    --paper: #fbfaf7;
    --concrete: #eeebe4;
    --concrete-2: #e2ddd1;
    --line: rgba(21, 24, 22, .14);
    --muted: #5d655f;
    --amber: #c7922e;
    --amber-deep: #8f6417;
    --amber-light: #e7bd62;

    --display: "Barlow Condensed", "Arial Narrow", system-ui, sans-serif;
    --text: "Barlow", system-ui, -apple-system, "Segoe UI", sans-serif;

    --pad: clamp(18px, 3.2vw, 56px);
    --radius: 4px;
    --sticky-top: var(--site-header-height, 0px);

    width: 100%;
    background: var(--paper);
    color: var(--ink);
    font-family: var(--text);
    overflow-x: clip;
}
.home-v2 img { max-width: 100%; }
.home-v2 a { color: inherit; }
.home-v2 svg { flex: 0 0 auto; }
.home-v2 :focus-visible { outline: 3px solid var(--amber); outline-offset: 3px; }

/* ---------- shared pieces ---------- */
.home-v2 .ed-wrap { width: 100%; max-width: none; margin-inline: 0; padding-inline: var(--pad); }
.home-v2 .ed-section { padding: clamp(56px, 7vw, 104px) 0; }
.home-v2 .ed-lead { max-width: 85ch; color: var(--muted); font-size: 18px; line-height: 1.7; }
.home-v2 .cm-muted,
.home-v2 .muted { color: var(--muted); }

.home-v2 .ed-label {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 0 12px;
    color: var(--amber-deep);
    font: 600 15px/1.3 var(--text);
}
.home-v2 .ed-label::before { content: ""; width: 28px; height: 2px; background: var(--amber); }

.home-v2 .cm-btn,
.home-v2 .cm-btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 52px;
    padding: 0 24px;
    border-radius: var(--radius);
    font: 600 16px/1 var(--text);
    text-decoration: none;
    cursor: pointer;
    transition: background .2s ease, color .2s ease, border-color .2s ease;
}
.home-v2 .cm-btn { background: var(--ink); border: 2px solid var(--ink); color: #fff; }
.home-v2 .cm-btn:hover { background: var(--amber); border-color: var(--amber); color: #15130c; }
.home-v2 .cm-btn[disabled] { opacity: .65; cursor: wait; }
.home-v2 .cm-btn-outline { background: transparent; border: 2px solid var(--ink); color: var(--ink); }
.home-v2 .cm-btn-outline:hover { background: var(--ink); color: #fff; }

.home-v2 .cm-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--amber-deep);
    font: 600 14px/1.2 var(--text);
    text-decoration: none;
}
.home-v2 a.cm-tag:hover { color: var(--ink); }

/* ---------- hero (index) ---------- */
.home-v2 .proj-hero {
    position: relative;
    isolation: isolate;
    display: flex;
    align-items: flex-end;
    min-height: clamp(420px, 56vh, 560px);
    background: #111;
    color: #fff;
}
.home-v2 .proj-hero > img {
    position: absolute;
    inset: 0;
    z-index: -2;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 50%;
    filter: saturate(.85) contrast(1.05);
}
.home-v2 .proj-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background:
        linear-gradient(90deg, rgba(10,13,11,.9) 0%, rgba(10,13,11,.58) 50%, rgba(10,13,11,.14) 90%),
        linear-gradient(0deg, rgba(10,13,11,.85) 0%, transparent 60%);
}
.home-v2 .proj-hero-inner { width: 100%; padding-top: 120px; padding-bottom: clamp(36px, 5vw, 64px); }
.home-v2 .proj-hero-label { margin: 0 0 18px; color: var(--amber-light); font: 600 16px/1.3 var(--text); }
.home-v2 .proj-hero h1 {
    max-width: 18ch;
    margin: 0;
    font: 700 clamp(52px, 7.6vw, 112px)/.9 var(--display);
    letter-spacing: -.015em;
}
.home-v2 .proj-hero h1 span {
    display: block;
    margin-top: 14px;
    color: rgba(255,255,255,.78);
    font: 500 clamp(26px, 3vw, 42px)/1.08 var(--display);
    letter-spacing: 0;
}
.home-v2 .proj-hero .ed-lead { margin: 22px 0 0; max-width: 70ch; color: rgba(255,255,255,.84); font-size: 19px; }

/* ---------- toolbar ---------- */
.home-v2 .cm-toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: stretch; }
.home-v2 .cm-toolbar input[type="search"] {
    flex: 1 1 320px;
    min-width: 0;
    min-height: 56px;
    padding: 0 18px;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    color: var(--ink);
    font: 500 17px/1 var(--text);
}
.home-v2 .cm-toolbar input[type="search"]:focus { border-color: var(--ink); outline: 3px solid rgba(199,146,46,.35); outline-offset: 0; }
.home-v2 .cm-toolbar .cm-btn { min-height: 56px; padding-inline: 32px; }

.home-v2 .cm-mode { display: inline-flex; padding: 4px; background: var(--concrete); border: 1px solid var(--line); border-radius: var(--radius); }
.home-v2 .cm-mode label {
    display: inline-flex;
    align-items: center;
    padding: 0 22px;
    border-radius: 2px;
    color: var(--ink);
    font: 600 16px/1 var(--text);
    cursor: pointer;
    transition: background .2s ease, color .2s ease;
}
.home-v2 .cm-mode label:hover { background: rgba(21,24,22,.08); }
.home-v2 .cm-mode label.is-active,
.home-v2 .cm-mode label:has(input:checked) { background: var(--ink); color: #fff; }
.home-v2 .cm-mode label:has(input:focus-visible) { outline: 3px solid var(--amber); outline-offset: 2px; }

.home-v2 .cm-note {
    margin-top: 16px;
    padding: 14px 18px;
    background: var(--concrete);
    border-left: 4px solid var(--amber);
    color: var(--muted);
    font-size: 16px;
    line-height: 1.55;
}

/* ---------- product grid + generic card ---------- */
.home-v2 .cm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 36px 20px; min-width: 0; }
.home-v2 .cm-grid > * { min-width: 0; }
/* ---------- product card ---------- */
.home-v2 .pc { display: flex; flex-direction: column; min-width: 0; background: transparent; border: 0; cursor: pointer; }
.home-v2 .pc-img { overflow: hidden; aspect-ratio: 4 / 3; background: var(--concrete-2); border-radius: var(--radius); }
.home-v2 .pc-img img { display: block; width: 100%; height: 100%; max-width: none; object-fit: cover; transition: transform .5s ease; }
.home-v2 .pc:hover .pc-img img { transform: scale(1.04); }
.home-v2 .pc-body { display: flex; flex: 1; flex-direction: column; gap: 4px; padding: 14px 0 0; }
.home-v2 .pc-body h3 { margin: 0; font: 600 17px/1.3 var(--text); }
.home-v2 .pc-price { display: flex; flex-wrap: wrap; align-items: baseline; gap: 0 6px; margin-top: auto; padding: 0; border: 0; }
.home-v2 .pc-price strong { font: 600 16px/1.4 var(--text); }
.home-v2 .pc-price span { color: var(--muted); font-size: 14px; }
.home-v2 .pc-body form { margin: 10px 0 0; }
.home-v2 .pc-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 44px;
    margin-top: 10px;
    padding: 0 16px;
    background: transparent;
    border: 1px solid var(--ink);
    border-radius: var(--radius);
    color: var(--ink);
    font: 600 15px/1 var(--text);
    text-decoration: none;
    cursor: pointer;
    transition: background .2s ease, color .2s ease;
}
.home-v2 .pc-body form .pc-btn { margin-top: 0; }
.home-v2 .pc-btn:hover { background: var(--ink); color: #fff; }
.home-v2 .pc-btn[disabled] { opacity: .7; cursor: wait; }
.home-v2 .pc:focus-visible { outline: 3px solid var(--amber); outline-offset: 6px; }

.home-v2 .resources-empty {
    grid-column: 1 / -1;
    padding: clamp(32px, 5vw, 64px);
    background: var(--concrete);
    border: 1px dashed var(--line);
    border-radius: var(--radius);
}
.home-v2 .resources-empty h2 { margin: 0 0 8px; font: 700 clamp(30px, 3.4vw, 44px)/1 var(--display); }
.home-v2 .resources-empty p { margin: 0 0 22px; color: var(--muted); font-size: 17px; }

.home-v2 .cm-pagination { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-top: 40px; padding-top: 24px; border-top: 2px solid var(--ink); }
.home-v2 .cm-pagination .status { color: var(--muted); font-size: 16px; }

/* ---------- floating request list ---------- */
.home-v2 .market-floating-cart-v2 {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 40;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: var(--ink);
    border-radius: var(--radius);
    box-shadow: 0 10px 30px rgba(10,13,11,.28);
    color: #fff;
    font: 600 16px/1 var(--text);
    text-decoration: none;
    transition: background .2s ease;
}
.home-v2 .market-floating-cart-v2:hover { background: var(--ink-2); }
.home-v2 .market-floating-cart-v2 svg { width: 22px; height: 22px; }
.home-v2 .market-floating-cart-v2 strong {
    display: inline-grid;
    place-items: center;
    min-width: 26px;
    height: 26px;
    padding: 0 7px;
    background: var(--amber);
    border-radius: 999px;
    color: #15130c;
    font-size: 14px;
}
.home-v2 .market-floating-cart-v2 em { color: rgba(255,255,255,.72); font-style: normal; font-size: 15px; font-weight: 500; }

/* ---------- product detail ---------- */
.home-v2 .cm-back { display: inline-flex; margin-bottom: 24px; }

.home-v2 .cm-detail {
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
    gap: clamp(28px, 5vw, 80px);
    align-items: start;
}
.home-v2 .cm-detail > * { min-width: 0; }

.home-v2 .cm-gallery-main {
    overflow: hidden;
    aspect-ratio: 4 / 3;
    background: var(--concrete-2);
    border: 1px solid var(--line);
    border-radius: var(--radius);
}
.home-v2 .cm-gallery-main img { display: block; width: 100%; height: 100%; max-width: none; object-fit: cover; }

.home-v2 .cm-thumbs { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 10px; margin-top: 10px; }
.home-v2 .cm-thumbs button {
    display: block;
    width: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    background: var(--concrete-2);
    border: 2px solid transparent;
    border-radius: var(--radius);
    cursor: pointer;
    opacity: .78;
    transition: opacity .2s ease, border-color .2s ease;
}
.home-v2 .cm-thumbs button:hover { opacity: 1; }
.home-v2 .cm-thumbs button.is-active { opacity: 1; border-color: var(--amber); }
.home-v2 .cm-thumbs img { display: block; width: 100%; height: 100%; max-width: none; object-fit: cover; }

.home-v2 .cm-info { position: sticky; top: calc(var(--sticky-top) + 24px); }
.home-v2 .cm-info h1 {
    margin: 4px 0 16px;
    font: 700 clamp(40px, 4.8vw, 68px)/.95 var(--display);
    letter-spacing: -.01em;
}
.home-v2 .cm-info .ed-lead { margin: 0; font-size: 18px; }

.home-v2 .cm-pricebox { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); margin-top: 26px; border-top: 2px solid var(--ink); }
.home-v2 .cm-pricebox > div { display: flex; flex-direction: column; gap: 6px; padding: 18px 20px 18px 0; border-bottom: 1px solid var(--line); }
.home-v2 .cm-pricebox > div + div { padding-left: 20px; border-left: 1px solid var(--line); }
.home-v2 .cm-pricebox strong { font: 700 clamp(30px, 3.2vw, 42px)/1 var(--display); }
.home-v2 .cm-pricebox .muted { font-size: 15px; line-height: 1.4; }

.home-v2 .cm-facts { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 18px; }
.home-v2 .cm-facts span { padding: 8px 14px; background: var(--concrete); border-radius: 999px; font: 500 14px/1 var(--text); }

.home-v2 .form-card { margin-top: 26px; padding: clamp(20px, 2.6vw, 32px); background: #fff; border: 1px solid var(--line); border-radius: var(--radius); }
.home-v2 .form-card h2 { margin: 0 0 8px; font: 700 clamp(28px, 2.6vw, 36px)/1 var(--display); }
.home-v2 .form-card > .cm-muted { margin: 0; font-size: 16px; line-height: 1.65; }

.home-v2 .cm-reqgrid { display: grid; gap: 16px; margin-top: 20px; }
.home-v2 .cm-reqgrid.is-split { grid-template-columns: repeat(2, minmax(0, 1fr)); }

.home-v2 .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
.home-v2 .form-grid .full { grid-column: 1 / -1; }
.home-v2 .form-grid label { display: grid; gap: 6px; min-width: 0; }
.home-v2 .form-grid label span { color: var(--ink); font: 600 14px/1.2 var(--text); }
.home-v2 .form-grid input {
    width: 100%;
    min-height: 48px;
    padding: 0 14px;
    background: var(--paper);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    color: var(--ink);
    font: 500 16px/1 var(--text);
}
.home-v2 .form-grid input:focus { border-color: var(--ink); outline: 3px solid rgba(199,146,46,.35); outline-offset: 0; }

.home-v2 .cm-card { padding: 20px; background: var(--concrete); border-radius: var(--radius); }
.home-v2 .cm-card h3 { margin: 0 0 8px; font: 700 28px/1 var(--display); }
.home-v2 .cm-card p { margin: 0; line-height: 1.65; }

.home-v2 .cm-related { margin-top: clamp(56px, 7vw, 96px); padding-top: clamp(32px, 4vw, 56px); border-top: 2px solid var(--ink); }
.home-v2 .cm-related h2 { margin: 0 0 28px; font: 700 clamp(34px, 4vw, 56px)/.98 var(--display); letter-spacing: -.01em; }

/* ---------- minimal overrides ---------- */
.home-v2 .cm-head { padding: clamp(40px, 6vw, 88px) 0 0; }
.home-v2 .cm-head h1 { margin: 0; font: 700 clamp(44px, 6vw, 84px)/.95 var(--display); letter-spacing: -.01em; }
.home-v2 .cm-head + .ed-section { padding-top: 28px; }
.home-v2 .market-floating-cart-v2 span { display: none; }

.home-v2 .cm-cat { margin: 0 0 8px; color: var(--muted); font-size: 15px; }
.home-v2 .cm-info h1 { margin: 0 0 14px; font: 700 clamp(36px, 4.2vw, 56px)/1 var(--display); }
.home-v2 .cm-price { display: flex; flex-wrap: wrap; align-items: baseline; gap: 0 8px; margin: 0 0 20px; }
.home-v2 .cm-price strong { font: 700 clamp(30px, 3vw, 40px)/1.1 var(--display); }
.home-v2 .cm-price span { color: var(--muted); font-size: 16px; }
.home-v2 .cm-sum { max-width: 52ch; margin: 0 0 28px; color: var(--muted); font-size: 17px; line-height: 1.65; }
.home-v2 .cm-buy form { display: grid; gap: 12px; }
.home-v2 .cm-buy form.is-inline { grid-template-columns: 110px minmax(0, 1fr); align-items: end; }
.home-v2 .cm-buy form.is-inline .cm-btn { margin: 0 !important; }
.home-v2 .cm-buy .cm-btn,
.home-v2 .cm-buy .cm-btn-outline { width: 100%; margin: 0 !important; }
.home-v2 .cm-fine { margin: 14px 0 0; color: var(--muted); font-size: 14px; }
.home-v2 .cm-related h2 { margin-bottom: 24px; font-size: clamp(30px, 3.4vw, 44px); }

/* ---------- responsive ---------- */
@media (max-width: 1100px) {
    .home-v2 .cm-reqgrid.is-split { grid-template-columns: 1fr; }
}
@media (max-width: 900px) {
    .home-v2 .cm-detail { grid-template-columns: 1fr; }
    .home-v2 .cm-info { position: static; }
}
@media (max-width: 680px) {
    .home-v2 .proj-hero { min-height: 0; }
    .home-v2 .proj-hero-inner { padding-top: 100px; }
    .home-v2 .proj-hero h1 { font-size: clamp(48px, 14vw, 72px); }
    .home-v2 .proj-hero .ed-lead { font-size: 17px; }
    .home-v2 .cm-toolbar .cm-mode,
    .home-v2 .cm-toolbar .cm-btn { width: 100%; }
    .home-v2 .cm-mode label { flex: 1; justify-content: center; min-height: 48px; }
    .home-v2 .cm-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .home-v2 .pc-body h3 { font-size: 15px; }
    .home-v2 .cm-grid { gap: 28px 12px; }
    .home-v2 .cm-thumbs { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .home-v2 .cm-pricebox > div + div { padding-left: 0; border-left: 0; }
    .home-v2 .market-floating-cart-v2 { right: 12px; bottom: 12px; left: 12px; justify-content: space-between; }
    .home-v2 .market-floating-cart-v2 em { margin-left: auto; }
    .home-v2 .cm-pagination .cm-btn,
    .home-v2 .cm-pagination .cm-btn-outline { flex: 1; }
}
@media (max-width: 420px) {
    .home-v2 .cm-grid { grid-template-columns: 1fr; }
}
@media (prefers-reduced-motion: reduce) {
    .home-v2 *, .home-v2 *::before, .home-v2 *::after { scroll-behavior: auto !important; transition: none !important; animation: none !important; }
}
</style>

<div class="home-v2">
    @php
        $cartItems = collect(session('cart.items', []))->values();
        $cartCount = $globalCartCount ?? $cartItems->sum(fn ($item) => (int) ($item['quantity'] ?? 1));
        $cartTotal = $cartItems->sum(fn ($item) => (int) ($item['line_total'] ?? 0));
        $mode = request('mode');
    @endphp

    {{-- HEADER --}}
    <header class="cm-head">
        <div class="ed-wrap">
            <h1 data-reveal>Materials &amp; equipment</h1>
        </div>
    </header>

    {{-- CATALOGUE --}}
    <section class="ed-section" aria-label="Catalogue">
        <div class="ed-wrap">
            <form method="GET" action="{{ route('products.index') }}" class="cm-toolbar" data-reveal>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search materials, tools, or equipment…" aria-label="Search resources">
                <div class="cm-mode" role="group" aria-label="Choose resource type">
                    <label @class(['is-active' => $mode === 'buy' || ! $mode])>
                        <input type="radio" name="mode" value="buy" @checked($mode === 'buy' || ! $mode) hidden>
                        <span>Materials</span>
                    </label>
                    <label @class(['is-active' => $mode === 'rent'])>
                        <input type="radio" name="mode" value="rent" @checked($mode === 'rent') hidden>
                        <span>Equipment</span>
                    </label>
                </div>
                <button class="cm-btn" type="submit">Search</button>
            </form>

            <div class="cm-grid" style="margin-top:32px;">
                @forelse ($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="resources-empty">
                        <h2>No matching resources</h2>
                        <p>Try another search term or switch between materials and equipment.</p>
                        <a class="cm-btn-outline" href="{{ route('products.index') }}">Reset filters</a>
                    </div>
                @endforelse
            </div>

            @if (($totalPages ?? 1) > 1)
                <nav class="cm-pagination" aria-label="Resource pages">
                    @if ($currentPage > 1)
                        <a class="cm-btn-outline" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">Previous</a>
                    @endif
                    <span class="status">Page {{ $currentPage }} of {{ $totalPages }}</span>
                    @if ($currentPage < $totalPages)
                        <a class="cm-btn" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">Next resources</a>
                    @endif
                </nav>
            @endif
        </div>
    </section>

    <a class="market-floating-cart-v2" href="{{ route('cart.index') }}" aria-label="Open request list">
        <x-nav-icon name="cart" />
        <span>Request list</span>
        <strong data-cart-count>{{ $cartCount }}</strong>
        <em data-cart-total>TZS {{ number_format($cartTotal) }}</em>
    </a>
</div>
@endsection

@push('scripts')
    <script>
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