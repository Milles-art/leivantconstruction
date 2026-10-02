@php
    $links = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Services', 'route' => 'services.index'],
        ['label' => 'Products', 'route' => 'products.index'],
        ['label' => 'Solution', 'route' => 'solution.index'],
        ['label' => 'Discovery', 'route' => 'discovery.index'],
        ['label' => 'Contact', 'route' => 'contact.index'],
    ];
    $cartItems = collect(session('cart.items', []));
    $cartTotal = $cartItems->sum(fn ($item) => $item['line_total'] ?? ($item['price'] * $item['quantity'] * max(1, $item['rental_days'] ?? 1)));
@endphp

<header class="site-chrome" x-data="{ menuOpen: false, cartOpen: false }" x-on:vant:navigated.window="menuOpen = false; cartOpen = false">
    <nav class="flex items-center justify-between px-5 sm:px-8 lg:px-12">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="site-brand-mark grid h-10 w-10 place-items-center text-xl">L</span>
            <span class="leading-tight">
                <span class="site-brand-title block text-xl">Leivant</span>
                <span class="site-brand-subtitle block -mt-1 text-[9px] font-bold uppercase">Construction Solutions</span>
            </span>
        </a>

        <div class="hidden items-center gap-8 lg:flex">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="site-nav-link {{ request()->routeIs($link['route']) ? 'is-active' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="site-icon-button relative grid h-10 w-10 place-items-center transition" x-on:click="cartOpen = true" aria-label="Open cart">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 6h15l-2 9H8L6 3H3" />
                    <circle cx="9" cy="20" r="1" />
                    <circle cx="18" cy="20" r="1" />
                </svg>
                <span class="absolute -right-1.5 -top-1.5 grid h-4 min-w-4 place-items-center rounded-full bg-vant-gold px-1 text-[9px] font-black text-white">{{ $cartItems->sum('quantity') }}</span>
            </button>

            @auth
                <a href="{{ route('dashboard') }}" class="vant-button-outline hidden px-4 py-2 text-sm sm:inline-flex">Account</a>
            @endauth

            <button type="button" class="site-icon-button grid h-10 w-10 place-items-center lg:hidden" x-on:click="menuOpen = ! menuOpen" aria-label="Toggle menu">
                <svg x-show="!menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg x-cloak x-show="menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </div>
    </nav>

    <div class="mobile-panel-pro lg:hidden" x-show="menuOpen" x-transition>
        <div class="grid gap-1 px-5 py-4">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="rounded-lg px-4 py-3 text-sm font-bold {{ request()->routeIs($link['route']) ? 'bg-vant-gold text-white' : 'text-zinc-700 hover:bg-amber-50 hover:text-vant-gold' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-lg px-4 py-3 text-sm font-bold text-zinc-700 hover:bg-amber-50 hover:text-vant-gold">Account</a>
            @endauth
        </div>
    </div>

    <div class="fixed inset-0 z-50" x-show="cartOpen" x-cloak>
        <div class="absolute inset-0 bg-black/75" x-on:click="cartOpen = false"></div>
        <aside class="cart-panel-pro absolute right-0 top-0 flex h-screen w-full max-w-md flex-col" x-transition>
            <div class="flex items-center justify-between border-b border-zinc-200 p-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-vant-gold">Cart</p>
                    <h2 class="text-3xl text-zinc-950">Tools & Equipment</h2>
                </div>
                <button type="button" class="site-icon-button p-2" x-on:click="cartOpen = false" aria-label="Close cart">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            <div class="flex-1 space-y-4 overflow-y-auto p-5">
                @forelse ($cartItems as $item)
                    <div class="flex gap-4 rounded-lg border border-zinc-200 bg-white p-3 shadow-sm">
                        <img src="{{ $item['image'] ?? '' }}" alt="{{ $item['name'] }}" class="h-16 w-16 rounded-md object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-bold text-zinc-950">{{ $item['name'] }}</p>
                            <p class="text-xs font-bold uppercase text-vant-gold">{{ ($item['purchase_type'] ?? 'buy') === 'rent' ? 'Rental' : 'Purchase' }}</p>
                            <p class="text-sm text-zinc-600">{{ $item['quantity'] }} x TZS {{ number_format($item['price']) }} / {{ $item['unit'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-vant-gold/40 bg-white p-6 text-center shadow-sm">
                        <p class="font-bold text-zinc-950">Your cart is ready for Leivant equipment.</p>
                        <p class="mt-2 text-sm text-zinc-600">Add tools or rental equipment from the Leivant catalog.</p>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-zinc-200 bg-white p-5">
                <div class="mb-4 flex items-center justify-between text-lg font-black">
                    <span>Total</span>
                    <span class="text-vant-gold">TZS {{ number_format($cartTotal) }}</span>
                </div>
                <a href="{{ route('cart.index') }}" class="vant-button w-full">View Cart</a>
            </div>
        </aside>
    </div>
</header>

