@php
    $title = trim($__env->yieldContent('title', 'Leivant Construction Solutions Company'));
    $description = trim($__env->yieldContent('meta_description', 'Leivant Construction Solutions Company plans, builds, renovates, supervises, and manages construction projects across Tanzania from Dar es Salaam.'));
    $canonical = trim($__env->yieldContent('canonical', url()->current()));
    $canonicalHost = parse_url(config('app.url', 'https://leivantconstruction.com'), PHP_URL_HOST) ?: 'leivantconstruction.com';
    $canonicalScheme = parse_url(config('app.url', 'https://leivantconstruction.com'), PHP_URL_SCHEME) ?: 'https';
    $canonical = preg_replace('#^https?://www\.'.preg_quote($canonicalHost, '#').'#i', $canonicalScheme.'://'.$canonicalHost, $canonical);
    $canonical = preg_replace('#^http://'.preg_quote($canonicalHost, '#').'#i', $canonicalScheme.'://'.$canonicalHost, $canonical);
    $ogImage = trim($__env->yieldContent('og_image', $ogImage ?? asset('leivant-webp/20260523_141609.jpg.webp')));
    $nav = [
        ['label' => 'Home', 'route' => 'home', 'active' => ['home'], 'icon' => 'home'],
        ['label' => 'Services', 'route' => 'services.index', 'active' => ['services.*'], 'icon' => 'services'],
        ['label' => 'Projects', 'route' => 'projects.index', 'active' => ['projects.*'], 'icon' => 'projects'],
        ['label' => 'Resources', 'route' => 'products.index', 'active' => ['products.*', 'cart.*', 'checkout.*', 'orders.*'], 'icon' => 'resources'],
        ['label' => 'Providers', 'route' => 'discovery.index', 'active' => ['discovery.*'], 'icon' => 'providers'],
        ['label' => 'About', 'route' => 'about.index', 'active' => ['about.*'], 'icon' => 'about'],
        ['label' => 'Contact', 'route' => 'contact.index', 'active' => ['contact.*'], 'icon' => 'contact'],
    ];
    $companyPhone = config('app.company.phone', '0717 970 799');
    $companyEmail = config('app.company.email', 'info@leivantconstruction.com');
    $companyLocation = 'Majumba site, Kipawa Airport, Dar es Salaam, Tanzania';
    $companyMapSearchUrl = 'https://www.google.com/maps/search/?api=1&query=Majumba%20site%20Kipawa%20Airport%20Dar%20es%20Salaam%20Tanzania';
    $configuredMapUrl = (string) config('app.company.map_url', $companyMapSearchUrl);
    $companyMapUrl = preg_match('/-6\.8664952|39\.1899987/', $configuredMapUrl) ? $companyMapSearchUrl : $configuredMapUrl;
    $whatsapp = config('app.company.whatsapp', 'https://wa.me/255717970799');
    $analytics = config('app.analytics', []);
    $socialLinks = array_filter(config('app.social', []));
    $cartCount = collect(session('cart.items', []))->sum(fn ($item) => (int) ($item['quantity'] ?? 1));
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    @hasSection('robots')
        <meta name="robots" content="@yield('robots')">
    @endif
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Leivant Construction Solutions">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('leivant-webp/20260523_141609.jpg.webp') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&family=Space+Grotesk:wght@500;600;700&family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&family=Space+Grotesk:wght@500;600;700&family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&family=Space+Grotesk:wght@500;600;700&family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="{{ asset('css/leivant-public.css') }}?v=20261012">
    <noscript><style>[data-reveal]{opacity:1 !important;transform:none !important;}</style></noscript>
    <meta name="theme-color" content="#002D72">
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Leivant Construction Solutions",
  "url": "https://leivantconstruction.com",
  "description": "Leivant Construction Solutions is a construction company in Dar es Salaam, Tanzania. We design, build, renovate, supervise, and deliver construction projects across Tanzania.",
  "telephone": "+255717970799",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Majumba site, Kipawa Airport",
    "addressLocality": "Dar es Salaam",
    "addressCountry": "TZ"
  },
  "openingHoursSpecification": [
    {"@type": "OpeningHoursSpecification", "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"], "opens": "08:00", "closes": "18:00"},
    {"@type": "OpeningHoursSpecification", "dayOfWeek": ["Saturday"], "opens": "09:00", "closes": "13:00"}
  ],
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Construction Services",
    "itemListElement": [
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Architecture & Design"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Construction & Delivery"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "BOQ Preparation"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Construction Supervision"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Construction Resource Coordination"}}
    ]
  }
}
    </script>
    @if (! empty($analytics['google_id']))
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $analytics['google_id'] }}');
            (function () {
                var loaded = false;
                function loadGoogleTag() {
                    if (loaded) return;
                    loaded = true;
                    var script = document.createElement('script');
                    script.async = true;
                    script.src = 'https://www.googletagmanager.com/gtag/js?id={{ $analytics['google_id'] }}';
                    document.head.appendChild(script);
                }
                function scheduleGoogleTag() {
                    window.setTimeout(loadGoogleTag, 5200);
                }
                window.addEventListener('pointerdown', loadGoogleTag, { once: true, passive: true });
                window.addEventListener('keydown', loadGoogleTag, { once: true });
                if (document.readyState === 'complete') {
                    scheduleGoogleTag();
                } else {
                    window.addEventListener('load', scheduleGoogleTag, { once: true });
                }
            })();
        </script>
    @endif
    @if (! empty($analytics['clarity_id']))
        <script>
            (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window, document, "clarity", "script", "{{ $analytics['clarity_id'] }}");
        </script>
    @endif
    @if (! empty($analytics['facebook_pixel_id']))
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $analytics['facebook_pixel_id'] }}');
            fbq('track', 'PageView');
        </script>
    @endif
    @stack('head')
</head>
<body>
    <a class="skip-link" href="#main">Skip to main content</a>
    <div class="site-loader" aria-hidden="true"></div>
    <header class="hub-header site-header" id="site-header">
        <div class="hub-topbar">
            <div class="hub-topbar-inner">
                <span class="hub-topbar-tag">Civil Works &amp; Construction Delivery</span>
                <div class="hub-topbar-meta">
                    <a href="tel:+255717970799"><x-nav-icon name="phone" /> {{ $companyPhone }}</a>
                    <span><x-nav-icon name="map" /> Dar es Salaam, Tanzania</span>
                </div>
            </div>
        </div>
        <div class="hub-navbar">
            <div class="hub-navbar-inner">
                <a href="{{ route('home') }}" class="hub-brand" aria-label="Leivant home">
                    <span class="brand-mark"><img src="{{ asset('images/leivant-logo-mark.svg') }}" alt="" width="40" height="40"></span>
                    <span class="brand-text"><strong>Leivant</strong><small>Construction Solutions</small></span>
                </a>
                <nav class="hub-nav-links" aria-label="Main navigation">
                    @foreach ($nav as $item)
                        <a href="{{ route($item['route']) }}" @class(['is-active' => request()->routeIs(...$item['active'])])>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
                <a href="{{ route('cart.index') }}" class="hub-cart-btn" aria-label="Request list">
                    <x-nav-icon name="cart" />
                    @if ($cartCount > 0)
                        <span class="hub-cart-count">{{ $cartCount }}</span>
                    @endif
                </a>
                <details class="hub-mobile mobile-menu" data-mobile-menu>
                    <summary aria-label="Menu" aria-expanded="false" aria-controls="mobile-panel"><x-nav-icon name="menu" /></summary>
                    <div class="mobile-panel" id="mobile-panel">
                        @foreach ($nav as $item)
                            <a href="{{ route($item['route']) }}" @class(['is-active' => request()->routeIs(...$item['active'])])>
                                <x-nav-icon :name="$item['icon']" />
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>
    </header>
    <main id="main">
        @if (session('success'))
            <div class="container flash-wrap"><div class="notice success">{{ session('success') }}</div></div>
        @endif
        @if (session('error'))
            <div class="container flash-wrap"><div class="notice danger">{{ session('error') }}</div></div>
        @endif
        @yield('content')
    </main>
    <footer class="hub-footer site-footer">
        <div class="footer-inner">
            <div class="footer-grid footer-grid-v2">
                <div class="footer-brand-col">
                    <a href="{{ route('home') }}" class="footer-brand">
                        <span class="footer-mark"><img src="{{ asset('images/leivant-logo-mark.svg') }}" alt="" width="36" height="36"></span>
                        <span>Leivant Construction</span>
                    </a>
                    <p class="footer-tagline">BRELA 626936 · Construction planning, building, and site delivery across Tanzania.</p>
                </div>
                <div>
                    <h3><x-nav-icon name="services" /> Explore</h3>
                    <a href="{{ route('services.index') }}"><x-nav-icon name="services" /><span>Services</span></a>
                    <a href="{{ route('projects.index') }}"><x-nav-icon name="projects" /><span>Projects</span></a>
                    <a href="{{ route('products.index') }}"><x-nav-icon name="resources" /><span>Resources</span></a>
                    <a href="{{ route('discovery.index') }}"><x-nav-icon name="providers" /><span>Providers</span></a>
                </div>
                <div>
                    <h3><x-nav-icon name="about" /> Company</h3>
                    <a href="{{ route('about.index') }}"><x-nav-icon name="about" /><span>About</span></a>
                    <a href="{{ route('contact.index') }}"><x-nav-icon name="contact" /><span>Contact</span></a>
                    <a href="{{ route('cart.index') }}"><x-nav-icon name="cart" /><span>Request list</span></a>
                    <a href="{{ route('privacy') }}"><x-nav-icon name="about" /><span>Privacy</span></a>
                </div>
            </div>
            <div class="legal legal-v2">
                <span>&copy; Leivant Construction Solutions</span>
                <span>Dar es Salaam, Tanzania</span>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('scroll', function () {
            document.getElementById('site-header')?.classList.toggle('is-scrolled', window.scrollY > 8);
        }, { passive: true });
        (function () {
            var loadingTimer;
            function showLoader() {
                clearTimeout(loadingTimer);
                document.body.classList.add('is-loading');
            }
            function hideLoader() {
                loadingTimer = setTimeout(function () {
                    document.body.classList.remove('is-loading');
                }, 120);
            }
            window.addEventListener('pageshow', hideLoader);
            window.addEventListener('load', hideLoader);
            window.addEventListener('beforeunload', showLoader);
            function trackLeadEvent(name, params) {
                if (typeof window.gtag !== 'function') return;
                window.gtag('event', name, Object.assign({
                    page_location: window.location.href,
                    page_title: document.title
                }, params || {}));
            }
            document.addEventListener('click', function (event) {
                var link = event.target.closest('a[href]');
                if (! link) return;
                var href = link.getAttribute('href') || '';
                var label = (link.textContent || '').trim().slice(0, 80);
                if (href.startsWith('tel:')) {
                    trackLeadEvent('phone_click', { link_url: href, link_text: label });
                } else if (href.includes('wa.me/')) {
                    trackLeadEvent('whatsapp_click', { link_url: href, link_text: label });
                } else if (href.startsWith('mailto:')) {
                    trackLeadEvent('email_click', { link_url: href, link_text: label });
                } else if (href.includes('/contact')) {
                    trackLeadEvent('contact_click', { link_url: href, link_text: label });
                } else if (href.includes('/products')) {
                    trackLeadEvent('product_navigation_click', { link_url: href, link_text: label });
                } else if (href.includes('/services')) {
                    trackLeadEvent('service_navigation_click', { link_url: href, link_text: label });
                }
            }, true);
            document.addEventListener('submit', function (event) {
                var form = event.target;
                if (! form) return;
                var action = form.getAttribute('action') || window.location.pathname;
                if (action.includes('/contact')) {
                    trackLeadEvent('contact_form_submit', { form_action: action });
                } else if (action.includes('/services/')) {
                    trackLeadEvent('service_request_submit', { form_action: action });
                } else if (action.includes('/checkout')) {
                    trackLeadEvent('checkout_submit', { form_action: action });
                }
            }, true);
            document.addEventListener('click', function (event) {
                var link = event.target.closest('a[href]');
                if (! link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
                if (link.target && link.target !== '_self') return;
                if (link.hasAttribute('download')) return;
                var href = link.getAttribute('href') || '';
                if (! href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:') || href.includes('wa.me/')) return;
                var next = new URL(href, window.location.href);
                if (next.href === window.location.href || (next.pathname === window.location.pathname && next.search === window.location.search && next.hash)) return;
                showLoader();
            });
            document.addEventListener('submit', function (event) {
                var form = event.target;
                if (! form || form.matches('.market-add-to-cart,[data-no-loader]')) return;
                showLoader();
            }, true);
            window.LeivantLoading = { show: showLoader, hide: hideLoader };
        })();
    </script>
    <script defer src="{{ asset('js/leivant-public.js') }}?v=20261007"></script>
    @stack('scripts')
</body>
</html>
