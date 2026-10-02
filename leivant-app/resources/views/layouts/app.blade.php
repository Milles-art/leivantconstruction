<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    {{-- ─── Character set & viewport ──────────────────────────────────────── --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
 
    {{-- ─── Security ───────────────────────────────────────────────────────── --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">
 
    {{-- ─── SEO & Social ───────────────────────────────────────────────────── --}}
    <title>@yield('title', config('app.name', 'Leivant Construction Solutions'))</title>
    <meta name="description" content="@yield('meta_description', 'Leivant Construction Solutions provides construction services, project coordination, tools and equipment, provider discovery, and service support across Tanzania.')">
    <meta name="author" content="Leivant Construction Solutions">
    <link rel="canonical" href="{{ url()->current() }}">
 
    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:title"       content="@yield('og_title', config('app.name', 'Leivant Construction Solutions'))">
    <meta property="og:description" content="@yield('og_description', 'Construction services, project coordination, and equipment across Tanzania.')">
    <meta property="og:image"       content="@yield('og_image', asset('leivant-webp/20260523_141609.jpg.webp'))">
    <meta property="og:locale"      content="en_TZ">
    <meta property="og:site_name"   content="{{ config('app.name', 'Leivant Construction Solutions') }}">
 
    {{-- Twitter / X --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('og_title', config('app.name', 'Leivant Construction Solutions'))">
    <meta name="twitter:description" content="@yield('og_description', 'Construction services, project coordination, and equipment across Tanzania.')">
    <meta name="twitter:image"       content="@yield('og_image', asset('leivant-webp/20260523_141609.jpg.webp'))">
 
    {{-- ─── Favicons ───────────────────────────────────────────────────────── --}}
    @php
        $faviconBust = file_exists(public_path('favicon.svg')) ? filemtime(public_path('favicon.svg')) : time();
    @endphp
    <link rel="icon"             type="image/svg+xml" href="{{ asset('favicon.svg') }}?v={{ $faviconBust }}">
    <link rel="icon"             type="image/png"     sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="apple-touch-icon"                      sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest"         href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#1A1612">
 
    {{-- ─── Fonts ──────────────────────────────────────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload"    href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"></noscript>
 
    {{-- ─── Assets (manual build only — Vite removed) ────────────────────── --}}
        @php
            $cssBust = file_exists(public_path('build/manual/app.css')) ? filemtime(public_path('build/manual/app.css')) : time();
            $jsBust  = file_exists(public_path('vendor/alpine.min.js'))  ? filemtime(public_path('vendor/alpine.min.js'))  : time();
        @endphp
        <link rel="stylesheet" href="{{ asset('build/manual/app.css') }}?v={{ $cssBust }}">
        <script defer src="{{ asset('vendor/alpine.min.js') }}?v={{ $jsBust }}"></script>
 
        {{-- ── Design tokens & base styles (fallback only) ────────────────── --}}
        <style>
            /* ── Suppress Flash of Un-styled Alpine content ─────────────── */
            [x-cloak] { display: none !important; }
 
            /* ── Design tokens ──────────────────────────────────────────── */
            :root {
                /* Brand palette */
                --color-primary:        #C8922A;
                --color-primary-dark:   #A67420;
                --color-primary-light:  #F5E6CA;
 
                /* Neutrals */
                --color-dark:           #1A1612;
                --color-dark-2:         #2D2620;
                --color-mid:            #5C4D3C;
                --color-muted:          #7A6A58;
 
                /* Surfaces */
                --color-surface:        #FDFAF6;
                --color-surface-2:      #F5EFE6;
                --color-border:         #E8DDD0;
                --color-white:          #FFFFFF;
 
                /* Elevation */
                --shadow-card:          0 2px 14px rgba(26, 22, 18, .08);
                --shadow-lift:          0 14px 38px rgba(26, 22, 18, .14);
 
                /* Radii */
                --radius-xs:            4px;
                --radius-sm:            6px;
                --radius-md:            12px;
                --radius-lg:            18px;
                --radius-pill:          9999px;
 
                /* Typography scale */
                --font-display:         'DM Serif Display', Georgia, 'Times New Roman', serif;
                --font-body:            'Inter', Arial, Helvetica, sans-serif;
                --text-xs:              .75rem;
                --text-sm:              .875rem;
                --text-base:            .9375rem;
                --text-lg:              1.125rem;
                --text-xl:              1.25rem;
                --text-2xl:             1.5rem;
                --text-3xl:             1.875rem;
                --text-4xl:             2.25rem;
 
                /* Spacing rhythm */
                --space-1:              .25rem;
                --space-2:              .5rem;
                --space-3:              .75rem;
                --space-4:              1rem;
                --space-6:              1.5rem;
                --space-8:              2rem;
                --space-12:             3rem;
                --space-16:             4rem;
 
                /* Transitions */
                --ease-out:             cubic-bezier(.16, 1, .3, 1);
                --transition-base:      .2s var(--ease-out);
            }
 
            /* ── Reduced-motion override ────────────────────────────────── */
            @media (prefers-reduced-motion: reduce) {
                *, *::before, *::after {
                    animation-duration:        .01ms !important;
                    animation-iteration-count: 1     !important;
                    transition-duration:       .01ms !important;
                    scroll-behavior:           auto  !important;
                }
            }
 
            /* ── Reset & base ───────────────────────────────────────────── */
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            html  { scroll-behavior: smooth; text-size-adjust: 100%; -webkit-text-size-adjust: 100%; }
            body  {
                background-color: var(--color-surface);
                color:            var(--color-dark);
                font-family:      var(--font-body);
                font-size:        var(--text-base);
                line-height:      1.65;
                -webkit-font-smoothing:  antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            img, video, svg { display: block; max-width: 100%; }
            a { color: inherit; text-decoration: none; }
            button { cursor: pointer; border: none; background: none; font-family: inherit; }
            :focus-visible {
                outline:        2px solid var(--color-primary);
                outline-offset: 3px;
                border-radius:  var(--radius-xs);
            }
 
            /* ── Display headings ───────────────────────────────────────── */
            h1, h2, h3, h4, h5, h6 {
                font-family:  var(--font-display);
                font-weight:  400;
                line-height:  1.2;
                letter-spacing: 0;
            }
 
            /* ── Layout helpers ─────────────────────────────────────────── */
            .vant-container {
                width:           100%;
                max-width:       80rem;
                margin-left:     auto;
                margin-right:    auto;
                padding-left:    var(--space-4);
                padding-right:   var(--space-4);
            }
            @media (min-width: 640px) {
                .vant-container { padding-left: var(--space-6); padding-right: var(--space-6); }
            }
            @media (min-width: 1024px) {
                .vant-container { padding-left: var(--space-8); padding-right: var(--space-8); }
            }
 
            /* ── Cards ──────────────────────────────────────────────────── */
            .vant-card {
                border-radius:   var(--radius-lg);
                border:          1px solid var(--color-border);
                background:      var(--color-white);
                box-shadow:      var(--shadow-card);
                overflow:        hidden;
                transition:      transform var(--transition-base),
                                 box-shadow var(--transition-base),
                                 border-color var(--transition-base);
            }
            .vant-card:hover {
                border-color: rgba(200, 146, 42, .5);
                box-shadow:   var(--shadow-lift);
                transform:    translateY(-2px);
            }
 
            /* ── Buttons ────────────────────────────────────────────────── */
            .vant-button,
            .vant-button-outline {
                display:        inline-flex;
                align-items:    center;
                justify-content: center;
                gap:            var(--space-2);
                border-radius:  var(--radius-pill);
                padding:        .8rem 1.4rem;
                font-family:    var(--font-body);
                font-size:      var(--text-sm);
                font-weight:    700;
                line-height:    1;
                white-space:    nowrap;
                transition:     background var(--transition-base),
                                color     var(--transition-base),
                                transform var(--transition-base),
                                box-shadow var(--transition-base);
            }
            .vant-button {
                background: var(--color-primary);
                color:      var(--color-white);
                border:     1px solid transparent;
                box-shadow: 0 2px 8px rgba(200, 146, 42, .28);
            }
            .vant-button:hover {
                background: var(--color-primary-dark);
                transform:  translateY(-1px);
                box-shadow: 0 4px 14px rgba(200, 146, 42, .38);
            }
            .vant-button:active  { transform: translateY(0); }
            .vant-button-outline {
                background: transparent;
                color:      var(--color-primary);
                border:     1px solid var(--color-primary);
            }
            .vant-button-outline:hover {
                background: var(--color-primary);
                color:      var(--color-white);
                transform:  translateY(-1px);
            }
 
            /* ── Form inputs ────────────────────────────────────────────── */
            .vant-input {
                display:       block;
                width:         100%;
                border-radius: var(--radius-sm);
                border:        1px solid var(--color-border);
                background:    var(--color-white);
                color:         var(--color-dark);
                padding:       .65rem .9rem;
                font-family:   var(--font-body);
                font-size:     var(--text-sm);
                line-height:   1.5;
                transition:    border-color var(--transition-base),
                               box-shadow   var(--transition-base);
            }
            .vant-input::placeholder { color: var(--color-muted); opacity: .7; }
            .vant-input:focus {
                outline:       none;
                border-color:  var(--color-primary);
                box-shadow:    0 0 0 3px rgba(200, 146, 42, .18);
            }
 
            /* ── Section bands ──────────────────────────────────────────── */
            .steel-band {
                background:    var(--color-dark);
                color:         var(--color-white);
                border-top:    1px solid rgba(255, 255, 255, .07);
                border-bottom: 1px solid rgba(255, 255, 255, .07);
            }
            .light-industrial-band {
                background: var(--color-surface);
                color:      var(--color-dark);
            }
 
            /* ── Hero overlay ───────────────────────────────────────────── */
            .concrete-overlay {
                background:
                    linear-gradient(rgba(26, 22, 18, .44), rgba(26, 22, 18, .58)),
                    radial-gradient(circle at 20% 20%, rgba(200, 146, 42, .22), transparent 32%),
                    radial-gradient(circle at 80% 10%, rgba(255, 255, 255, .12), transparent 26%);
            }
 
            /* ── Navigation chrome ──────────────────────────────────────── */
            .site-chrome {
                position:         fixed;
                inset:            0 0 auto;
                z-index:          50;
                border-bottom:    1px solid rgba(232, 221, 208, .85);
                background:       rgba(255, 255, 255, .92);
                backdrop-filter:  blur(14px);
                -webkit-backdrop-filter: blur(14px);
                box-shadow:       0 2px 14px rgba(26, 22, 18, .05);
            }
            .site-chrome nav { height: 72px; }
            .site-brand-mark {
                border-radius: var(--radius-md);
                background:    linear-gradient(135deg, var(--color-dark-2), var(--color-primary));
                color:         var(--color-white);
                font-family:   var(--font-display);
            }
            .site-brand-title {
                color:       var(--color-dark);
                font-family: var(--font-display);
            }
            .site-brand-subtitle {
                color:           var(--color-muted);
                letter-spacing:  .18em;
                text-transform:  uppercase;
                font-size:       var(--text-xs);
            }
            .site-nav-link {
                display:       inline-flex;
                align-items:   center;
                height:        72px;
                color:         var(--color-muted);
                font-size:     var(--text-sm);
                font-weight:   600;
                border-bottom: 2px solid transparent;
                transition:    color var(--transition-base),
                               border-color var(--transition-base);
            }
            .site-nav-link:hover,
            .site-nav-link.is-active {
                color:         var(--color-primary);
                border-bottom-color: var(--color-primary);
            }
            .site-icon-button {
                display:       inline-flex;
                align-items:   center;
                justify-content: center;
                border:        1px solid var(--color-border);
                border-radius: var(--radius-pill);
                background:    var(--color-white);
                color:         var(--color-dark);
                box-shadow:    0 1px 6px rgba(26, 22, 18, .05);
                transition:    border-color var(--transition-base),
                               color        var(--transition-base);
            }
            .site-icon-button:hover {
                border-color: var(--color-primary);
                color:        var(--color-primary);
            }
 
            /* ── Mobile panel ───────────────────────────────────────────── */
            .mobile-panel-pro {
                border-top: 1px solid var(--color-border);
                background: var(--color-white);
                box-shadow: var(--shadow-lift);
            }
 
            /* ── Cart / drawer panel ────────────────────────────────────── */
            .cart-panel-pro {
                background:  var(--color-surface);
                color:       var(--color-dark);
                border-left: 1px solid var(--color-border);
                box-shadow:  -18px 0 42px rgba(26, 22, 18, .16);
            }
 
            /* ── Footer ─────────────────────────────────────────────────── */
            .footer-pro {
                background: var(--color-dark);
                color:      var(--color-white);
            }
            .footer-pro a:hover { color: var(--color-primary); }
 
            /* ── Page-load progress indicator ───────────────────────────── */
            .vant-progress {
                position:   fixed;
                top:        0;
                left:       0;
                right:      0;
                height:     3px;
                z-index:    9999;
                background: linear-gradient(90deg, var(--color-primary), var(--color-primary-dark));
                transform:  scaleX(0);
                transform-origin: left center;
                transition: transform .3s var(--ease-out);
            }
            .vant-progress.is-loading { animation: vant-progress-bar 1s ease-in-out infinite; }
 
            @keyframes vant-progress-bar {
                0%   { transform: scaleX(0);   opacity: 1; }
                60%  { transform: scaleX(.85); opacity: 1; }
                100% { transform: scaleX(1);   opacity: 0; }
            }
 
            /* ── Page-transition dimming ────────────────────────────────── */
            body.vant-loading main {
                opacity:         .4;
                pointer-events:  none;
                transition:      opacity .18s ease;
            }
 
            /* ── Skip-to-content (accessibility) ────────────────────────── */
            .skip-link {
                position:    absolute;
                top:         -100%;
                left:        var(--space-4);
                background:  var(--color-primary);
                color:       var(--color-white);
                padding:     var(--space-2) var(--space-4);
                border-radius: 0 0 var(--radius-sm) var(--radius-sm);
                font-weight: 700;
                font-size:   var(--text-sm);
                z-index:     9999;
                transition:  top .15s ease;
            }
            .skip-link:focus { top: 0; }
        </style>

    {{-- ─── Page-level head overrides ──────────────────────────────────────── --}}
    @stack('head')
</head>
<body>
    {{-- ── Accessibility: skip navigation ──────────────────────────────────── --}}
    <a href="#vant-page" class="skip-link">Skip to content</a>
 
    {{-- ── Page-load progress bar ──────────────────────────────────────────── --}}
    <div class="vant-progress" id="vant-progress" aria-hidden="true"></div>
 
    {{-- ── Site navigation ─────────────────────────────────────────────────── --}}
    <x-navbar />
 
    {{-- ── Main content ─────────────────────────────────────────────────────── --}}
    <main id="vant-page" data-vant-page class="min-h-screen pt-[72px]" tabindex="-1">
        <x-alert />
        @yield('content')
    </main>
 
    {{-- ── Site footer ──────────────────────────────────────────────────────── --}}
    <x-footer />
 
    {{-- ── Core scripts ─────────────────────────────────────────────────────── --}}
    <script src="{{ asset('js/vant-live-navigation.js') }}" defer></script>
 
    {{-- ── Page-level scripts ───────────────────────────────────────────────── --}}
    @stack('scripts')
</body>
</html>