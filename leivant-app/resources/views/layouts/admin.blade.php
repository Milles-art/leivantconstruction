@php
    $navSections = [
        'Command' => [
            ['Dashboard', 'admin.dashboard', 'admin.dashboard', 'dashboard', 'Operations overview', null, 'dashboard.view'],
            ['Inquiries', 'admin.inquiries.index', 'admin.inquiries.*', 'mail', 'Client requests', null, 'pipeline.manage'],
            ['Email', 'admin.mail-inbox.index', 'admin.mail-inbox.*', 'mail', 'Full mailbox client', null, 'mail.manage'],
            ['Projects', 'admin.projects.index', 'admin.projects.*', 'briefcase', 'Construction brief submissions', null, 'projects.manage'],
            ['Orders', 'admin.orders.index', 'admin.orders.*', 'receipt', 'Read-only customer orders', null, 'orders.view'],
        ],
        'Resources' => [
            ['Products', 'admin.products.index', 'admin.products.*', 'box', 'Tools and equipment', null, 'catalog.manage'],
            ['Categories', 'admin.categories.index', 'admin.categories.*', 'tag', 'Resource categories', null, 'catalog.manage'],
            ['Reviews', 'admin.reviews.index', 'admin.reviews.*', 'star', 'Customer review moderation', null, 'reviews.manage'],
        ],
        'Network' => [
            ['Providers', 'admin.providers.index', 'admin.providers.*', 'users', 'Discovery listings', null, 'providers.manage'],
            ['Regions', 'admin.regions.index', 'admin.regions.*', 'map', 'Service regions', null, 'catalog.manage'],
            ['Services', 'admin.services.index', 'admin.services.*', 'clipboard', 'Company services', null, 'catalog.manage'],
        ],
        'System' => [
            ['Users', 'admin.users.index', 'admin.users.*', 'shield', 'Admins and provider accounts', null, 'system.manage'],
            ['Settings', 'admin.settings.edit', 'admin.settings.*', 'settings', 'Company and site settings', null, 'system.manage'],
            ['Activity', 'admin.activity.index', 'admin.activity.*', 'activity', 'Admin change log', null, 'system.manage'],
        ],
    ];

    $canAdmin = fn (string $permission): bool => (bool) auth()->user()?->hasAdminPermission($permission);

    $primaryNav = ['Dashboard', 'Inquiries', 'Email', 'Projects', 'Orders', 'Products', 'Providers', 'Reviews'];
    $visibleNavLinks = collect($navSections)
        ->flatMap(fn ($links) => $links)
        ->filter(fn ($link) => $canAdmin($link[6]))
        ->values();
    $primaryNavLinks = $visibleNavLinks
        ->filter(fn ($link) => in_array($link[0], $primaryNav, true))
        ->sortBy(fn ($link) => array_search($link[0], $primaryNav, true))
        ->values();
    $secondaryNavLinks = $visibleNavLinks
        ->reject(fn ($link) => in_array($link[0], $primaryNav, true))
        ->values();
    $secondaryNavActive = $secondaryNavLinks->contains(fn ($link) => request()->routeIs($link[2]));

    $adminIcon = function (string $icon): string {
        return match ($icon) {
            'box' => '<path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="M12 12 4.4 7.8"/><path d="M12 12l7.6-4.2"/><path d="M12 12v8.5"/>',
            'clipboard' => '<path d="M9 4h6"/><path d="M9 4a2 2 0 0 0-2 2v1h10V6a2 2 0 0 0-2-2"/><path d="M7 6H5a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-2"/><path d="M8 13h8"/><path d="M8 17h5"/>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><path d="M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'receipt' => '<path d="M4 3v18l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V3H4Z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/>',
            'mail' => '<path d="M4 5h16v14H4V5Z"/><path d="m4 7 8 6 8-6"/>',
            'briefcase' => '<path d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1"/><path d="M4 7h16v12H4V7Z"/><path d="M4 12h16"/><path d="M9 12v2h6v-2"/>',
            'tag' => '<path d="M20.6 13.1 13.1 20.6a2 2 0 0 1-2.8 0L3 13.3V3h10.3l7.3 7.3a2 2 0 0 1 0 2.8Z"/><path d="M7.5 7.5h.01"/>',
            'map' => '<path d="M9 18 3 21V6l6-3 6 3 6-3v15l-6 3-6-3Z"/><path d="M9 3v15"/><path d="M15 6v15"/>',
            'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="M9 12l2 2 4-5"/>',
            'settings' => '<path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.1-1H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6A1.7 1.7 0 0 0 10.4 3V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.4.2.8.6 1 1H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1Z"/>',
            'activity' => '<path d="M3 12h4l3 8 4-16 3 8h4"/>',
            'star' => '<path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2Z"/>',
            default => '<path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/>',
        };
    };
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head><meta charset="utf-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Leivant Admin')</title>

            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="{{ asset('build/manual/app.css') }}?v={{ file_exists(public_path('build/manual/app.css')) ? filemtime(public_path('build/manual/app.css')) : time() }}-public-root">
            <script defer src="{{ asset('vendor/alpine.min.js') }}?v={{ file_exists(public_path('vendor/alpine.min.js')) ? filemtime(public_path('vendor/alpine.min.js')) : time() }}-public-root"></script>
            <style>
                [x-cloak] { display: none !important; }
                :root {
                    --admin-primary: #C8922A;
                    --admin-primary-dark: #A67420;
                    --admin-primary-light: #F5E6CA;
                    --admin-dark: #1A1612;
                    --admin-dark-2: #2D2620;
                    --admin-text: #161616;
                    --admin-muted: #60584D;
                    --admin-surface: #F4F1EA;
                    --admin-surface-2: #EAE2D3;
                    --admin-border: #CDBFAD;
                    --admin-good: #147A4D;
                    --admin-info: #276AA3;
                    --admin-warn: #B7791F;
                    --admin-danger: #B42318;
                    --admin-card-shadow: 0 14px 34px rgba(26,22,18,.08);
                }
                body {
                    background: var(--admin-surface) !important;
                    color: var(--admin-text) !important;
                    font-family: 'Inter', Arial, Helvetica, sans-serif;
                }
                h1, h2, h3, h4 {
                    font-family: 'DM Serif Display', serif;
                    font-weight: 400;
                    text-transform: none;
                    letter-spacing: 0;
                }
                .vant-button {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 100px;
                    background: var(--admin-primary);
                    padding: .7rem 1.05rem;
                    color: white;
                    font-size: .85rem;
                    font-weight: 700;
                    letter-spacing: 0;
                    min-height: 2.75rem;
                    transition: all .2s ease;
                }
                .vant-button:hover { background: var(--admin-primary-dark); color: white; transform: translateY(-1px); }
                .vant-button-outline {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 100px;
                    border: 1px solid var(--admin-primary);
                    padding: .7rem 1.05rem;
                    color: var(--admin-primary);
                    font-size: .85rem;
                    font-weight: 700;
                    letter-spacing: 0;
                    min-height: 2.75rem;
                    transition: all .2s ease;
                }
                .vant-button-outline:hover { background: var(--admin-primary); color: white; }
                .vant-container { width: 100%; max-width: 80rem; margin-left: auto; margin-right: auto; padding-left: 1rem; padding-right: 1rem; }
                .admin-content h1 { font-size: clamp(2rem, 3vw, 2.85rem) !important; line-height: 1.08 !important; color: var(--admin-dark) !important; }
                .admin-content h2 { font-size: clamp(1.35rem, 2vw, 1.85rem) !important; line-height: 1.15 !important; color: var(--admin-dark) !important; }
                .admin-content h3, .admin-content h4 { color: var(--admin-dark) !important; }
                .admin-content p { color: var(--admin-muted); }
                .admin-content table { background: white; border-color: var(--admin-border) !important; }
                .admin-content table thead { background: var(--admin-surface-2) !important; }
                .admin-content table th { color: var(--admin-muted) !important; font-size: .7rem !important; letter-spacing: .1em !important; }
                .admin-content table tbody tr { transition: background-color .18s ease; }
                .admin-content table tbody tr:hover { background: var(--admin-surface) !important; }
                .admin-content .rounded-lg, .admin-content .rounded-xl, .admin-content .rounded-2xl { border-radius: .75rem !important; }
                .admin-content .bg-white, .admin-content .bg-zinc-50, .admin-content .bg-white\/5, .admin-content .bg-white\/\[0\.03\] { background-color: white !important; }
                .admin-content [class*="bg-[#11161d]"],
                .admin-content [class*="bg-vant-card"],
                .admin-content [class*="bg-black/"],
                .admin-content [class*="bg-white/10"] {
                    background-color: white !important;
                }
                .admin-content .border-white\/10, .admin-content .border-zinc-200, .admin-content .border-zinc-800 { border-color: var(--admin-border) !important; }
                .admin-content .divide-white\/10 > :not([hidden]) ~ :not([hidden]) { border-color: var(--admin-border) !important; }
                .admin-content .text-white, .admin-content .text-black, .admin-content .text-zinc-100, .admin-content .text-zinc-200, .admin-content .text-zinc-950 { color: var(--admin-dark) !important; }
                .admin-content .text-zinc-300, .admin-content .text-zinc-400, .admin-content .text-zinc-500, .admin-content .text-zinc-600 { color: var(--admin-muted) !important; }
                .admin-content input,
                .admin-content select,
                .admin-content textarea,
                .admin-content .vant-input {
                    display: block;
                    width: 100%;
                    border-color: var(--admin-border) !important;
                    border-width: 1px !important;
                    border-style: solid !important;
                    border-radius: .5rem !important;
                    background: white !important;
                    color: var(--admin-text) !important;
                    min-height: 2.9rem;
                    padding: .68rem .85rem !important;
                    font-size: .92rem !important;
                    line-height: 1.35 !important;
                    box-shadow: inset 0 1px 0 rgba(255,255,255,.75);
                }
                .admin-content select,
                .admin-content select.vant-input {
                    appearance: auto !important;
                    padding-right: 2rem !important;
                }
                .admin-content textarea,
                .admin-content textarea.vant-input {
                    min-height: 8rem;
                    resize: vertical;
                }
                .admin-content input[type="checkbox"] {
                    display: inline-block;
                    width: 1rem;
                    height: 1rem;
                    min-height: 1rem;
                    padding: 0 !important;
                    border-radius: .25rem !important;
                    accent-color: var(--admin-primary);
                    vertical-align: middle;
                }
                .admin-content input[type="file"] {
                    min-height: 3rem;
                    padding: .45rem !important;
                }
                .admin-content input::placeholder,
                .admin-content textarea::placeholder {
                    color: #8A8175 !important;
                    opacity: 1;
                }
                .admin-content label > span:first-child {
                    color: var(--admin-dark) !important;
                    letter-spacing: 0;
                }
                .admin-content input:focus, .admin-content select:focus, .admin-content textarea:focus, .admin-content .vant-input:focus {
                    border-color: var(--admin-primary) !important;
                    box-shadow: 0 0 0 3px rgba(200,146,42,.12) !important;
                    outline: none !important;
                }
                .admin-nav-icon svg { height: .9rem; width: .9rem; stroke-width: 2; }
                .admin-shell {
                    background:
                        linear-gradient(135deg, rgba(200,146,42,.12), transparent 32rem),
                        linear-gradient(315deg, rgba(17,17,17,.06), transparent 34rem),
                        var(--admin-surface);
                }
                .admin-topbar {
                    background: rgba(255,255,255,.94);
                    border-color: rgba(232,221,208,.92) !important;
                    backdrop-filter: blur(14px);
                    box-shadow: 0 2px 14px rgba(26,22,18,.05);
                }
                .admin-brand-mark {
                    border-radius: 12px;
                    background: white;
                    border: 1px solid var(--admin-border);
                    padding: .2rem;
                    overflow: hidden;
                    box-shadow: 0 10px 22px rgba(166,116,32,.14);
                }
                .admin-brand-mark img {
                    height: 100%;
                    width: 100%;
                    object-fit: contain;
                }
                .admin-brand-title {
                    color: var(--admin-dark);
                    font-family: 'DM Serif Display', Georgia, serif;
                    font-size: 1.25rem;
                    line-height: 1;
                }
                .admin-brand-subtitle {
                    color: var(--admin-muted);
                    font-size: .58rem;
                    font-weight: 800;
                    letter-spacing: .18em;
                    text-transform: uppercase;
                }
                .admin-nav-strip {
                    border-bottom: 1px solid var(--admin-border);
                    background: rgba(255,255,255,.94);
                }
                .admin-nav-link {
                    display: inline-flex;
                    align-items: center;
                    gap: .45rem;
                    border-bottom: 2px solid transparent;
                    color: var(--admin-muted);
                    padding: .75rem .25rem;
                    font-size: .86rem;
                    font-weight: 700;
                    white-space: nowrap;
                    transition: all .16s ease;
                }
                .admin-nav-link:hover { color: var(--admin-primary); }
                .admin-nav-link.is-active {
                    border-bottom-color: var(--admin-primary);
                    color: var(--admin-primary);
                }
                .admin-nav-more summary::-webkit-details-marker { display: none; }
                .admin-nav-more[open] summary { color: var(--admin-primary); }
                .admin-nav-menu {
                    position: absolute;
                    right: 0;
                    top: calc(100% + .5rem);
                    z-index: 60;
                    display: grid;
                    min-width: 13.5rem;
                    gap: .25rem;
                    border: 1px solid var(--admin-border);
                    border-radius: .5rem;
                    background: white;
                    padding: .45rem;
                    box-shadow: 0 16px 38px rgba(26,22,18,.14);
                }
                .admin-nav-menu a {
                    display: flex;
                    align-items: center;
                    gap: .55rem;
                    border-radius: .45rem;
                    padding: .7rem .8rem;
                    color: var(--admin-muted);
                    font-size: .84rem;
                    font-weight: 800;
                }
                .admin-nav-menu a:hover,
                .admin-nav-menu a.is-active {
                    background: var(--admin-primary-light);
                    color: var(--admin-primary-dark);
                }
                .admin-hero {
                    border: 1px solid rgba(200,146,42,.42);
                    background:
                        linear-gradient(135deg, rgba(200,146,42,.24), transparent 42%),
                        #111111;
                    color: white;
                    border-radius: .65rem;
                    box-shadow: 0 18px 44px rgba(17,17,17,.18);
                    overflow: hidden;
                }
                .admin-hero h1, .admin-hero h2, .admin-hero h3, .admin-hero p { color: white !important; }
                .admin-hero .admin-eyebrow { color: #F5C76A !important; }
                .admin-card,
                .admin-panel {
                    border: 1px solid var(--admin-border);
                    background: #FFFFFF;
                    border-radius: .5rem;
                    box-shadow: var(--admin-card-shadow);
                    overflow: hidden;
                }
                .admin-card { border-top: 3px solid rgba(200,146,42,.78); }
                .admin-card-plain { border-top-width: 1px; }
                .admin-page-head {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                    justify-content: space-between;
                    border: 1px solid var(--admin-border);
                    border-radius: 12px;
                    background: white;
                    padding: 1.35rem;
                    box-shadow: var(--admin-card-shadow);
                }
                .admin-page-head h1 {
                    margin-top: .15rem;
                    font-size: clamp(2.15rem, 4vw, 3.65rem) !important;
                }
                .admin-page-head p { max-width: 48rem; }
                @media (min-width: 768px) {
                    .admin-page-head { flex-direction: row; align-items: flex-end; }
                }
                .admin-priority-card {
                    border: 1px solid var(--admin-border);
                    border-top: 4px solid var(--admin-primary);
                    border-radius: 10px;
                    background: white;
                    padding: 1rem;
                    box-shadow: var(--admin-card-shadow);
                    transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
                }
                .admin-priority-card:hover {
                    transform: translateY(-2px);
                    border-color: rgba(200,146,42,.6);
                    box-shadow: 0 14px 38px rgba(26,22,18,.14);
                }
                .admin-priority-card span {
                    display: block;
                    color: var(--admin-muted);
                    font-size: .72rem;
                    font-weight: 900;
                    letter-spacing: .08em;
                    text-transform: uppercase;
                }
                .admin-priority-card strong {
                    display: block;
                    margin-top: .4rem;
                    color: var(--admin-dark);
                    font-family: 'DM Serif Display', Georgia, serif;
                    font-size: 2.35rem;
                    font-weight: 400;
                    line-height: 1;
                }
                .admin-priority-card small {
                    display: block;
                    margin-top: .35rem;
                    color: var(--admin-primary-dark);
                    font-size: .78rem;
                    font-weight: 800;
                }
                .admin-metric-grid {
                    display: grid;
                    gap: .85rem;
                    grid-template-columns: repeat(auto-fit, minmax(13rem, 1fr));
                }
                .admin-metric-card {
                    display: flex;
                    min-height: 8rem;
                    flex-direction: column;
                    justify-content: space-between;
                    border: 1px solid var(--admin-border);
                    border-top: 4px solid var(--admin-primary);
                    border-radius: .5rem;
                    background: white;
                    padding: 1rem;
                    box-shadow: var(--admin-card-shadow);
                }
                .admin-metric-card span {
                    color: var(--admin-muted);
                    font-size: .68rem;
                    font-weight: 900;
                    letter-spacing: .12em;
                    text-transform: uppercase;
                }
                .admin-metric-card strong {
                    margin-top: .4rem;
                    color: var(--admin-dark);
                    font-family: 'DM Serif Display', Georgia, serif;
                    font-size: 2.55rem;
                    font-weight: 400;
                    line-height: 1;
                }
                .admin-metric-card small {
                    color: var(--admin-primary-dark);
                    font-size: .78rem;
                    font-weight: 800;
                }
                .admin-dashboard-grid {
                    display: grid;
                    gap: 1.25rem;
                }
                @media (min-width: 1024px) {
                    .admin-dashboard-grid {
                        grid-template-columns: minmax(0, 1fr) minmax(18rem, .42fr);
                        align-items: start;
                    }
                }
                .admin-status-card {
                    display: flex;
                    flex-direction: column;
                    gap: .85rem;
                    border: 1px solid var(--admin-border);
                    border-left: 5px solid var(--admin-primary);
                    border-radius: .5rem;
                    background: white;
                    padding: 1rem;
                    box-shadow: 0 10px 24px rgba(17,17,17,.08);
                }
                .admin-status-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: .8rem;
                    border-radius: .45rem;
                    background: var(--admin-surface);
                    padding: .75rem .85rem;
                }
                .admin-status-number {
                    min-width: 2.5rem;
                    border-radius: .4rem;
                    background: var(--admin-dark);
                    color: var(--admin-primary-light);
                    padding: .4rem .55rem;
                    text-align: center;
                    font-weight: 900;
                }
                .admin-filter-bar {
                    display: flex !important;
                    flex-wrap: wrap;
                    align-items: flex-end;
                    gap: .75rem;
                    border: 1px solid var(--admin-border);
                    border-radius: .5rem;
                    background: #FFFFFF;
                    padding: 1rem;
                    box-shadow: 0 8px 20px rgba(17,17,17,.06);
                }
                .admin-filter-bar > .vant-input,
                .admin-filter-bar > input,
                .admin-filter-bar > select {
                    flex: 1 1 12rem;
                    min-width: 0;
                }
                .admin-filter-bar > input.vant-input {
                    flex-basis: 18rem;
                }
                .admin-filter-bar > .vant-button,
                .admin-filter-bar > .vant-button-outline,
                .admin-filter-bar > button,
                .admin-filter-bar > a {
                    flex: 0 0 auto;
                    min-width: 7.5rem;
                    white-space: nowrap;
                }
                @media (max-width: 640px) {
                    .admin-filter-bar > * {
                        flex-basis: 100% !important;
                        width: 100% !important;
                    }
                }
                .admin-table-wrap {
                    overflow: hidden;
                    border: 1px solid var(--admin-border);
                    border-radius: .5rem;
                    background: white;
                    box-shadow: var(--admin-card-shadow);
                }
                .admin-badge {
                    display: inline-flex;
                    align-items: center;
                    border-radius: 999px;
                    padding: .28rem .62rem;
                    font-size: .68rem;
                    font-weight: 800;
                    line-height: 1;
                    text-transform: uppercase;
                }
                .admin-card-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                    border-bottom: 1px solid var(--admin-border);
                    padding: 1rem 1.25rem;
                    background: var(--admin-dark);
                }
                .admin-card-header h2, .admin-card-header p { color: white !important; }
                .admin-card-header .admin-eyebrow { color: #F5C76A !important; }
                .admin-list-link {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: .85rem;
                    padding: .9rem 1rem;
                    transition: background-color .18s ease;
                }
                .admin-list-link:hover { background: #FFF8EA; }
                .admin-action-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: .85rem;
                    border-bottom: 1px solid #EFE7DA;
                    padding: .85rem 0;
                }
                .admin-action-row:last-child { border-bottom: 0; }
                .admin-eyebrow {
                    color: var(--admin-primary);
                    font-size: .68rem;
                    font-weight: 800;
                    letter-spacing: .18em;
                    text-transform: uppercase;
                }
                .admin-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: .45rem;
                    border-radius: 999px;
                    border: 1px solid var(--admin-border);
                    background: white;
                    color: var(--admin-muted);
                    padding: .42rem .75rem;
                    font-size: .72rem;
                    font-weight: 800;
                    letter-spacing: .04em;
                    text-transform: uppercase;
                }
                .admin-live-dot {
                    display: inline-block;
                    width: .5rem;
                    height: .5rem;
                    border-radius: 999px;
                    background: var(--admin-good);
                    box-shadow: 0 0 0 4px rgba(20,122,77,.12);
                }
                .admin-module-link {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: .85rem;
                    border: 1px solid var(--admin-border);
                    border-radius: .8rem;
                    background: white;
                    color: var(--admin-dark);
                    padding: .85rem 1rem;
                    box-shadow: 0 8px 18px rgba(26,22,18,.04);
                    transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease;
                }
                .admin-module-link:hover {
                    transform: translateY(-1px);
                    border-color: rgba(200,146,42,.55);
                    box-shadow: 0 12px 28px rgba(26,22,18,.08);
                    color: var(--admin-primary-dark);
                }
                .admin-workspace-label { color: rgba(255,255,255,.42); font-size: .66rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
            </style>
        @stack('head')
    </head>
    <body class="min-h-screen bg-vant-black text-white antialiased">
        <div class="admin-shell min-h-screen">
            <header class="admin-topbar sticky top-0 z-40 border-b px-4 py-3 md:px-8">
                <div class="mx-auto flex max-w-7xl flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-3">
                        <span class="admin-brand-mark grid h-10 w-10 place-items-center text-xl"><img src="{{ asset('images/leivant-logo-mark.svg') }}" alt="" width="40" height="40"></span>
                        <span>
                            <span class="admin-brand-title block">Leivant</span>
                            <span class="admin-brand-subtitle block -mt-1">Admin Workspace</span>
                        </span>
                    </a>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="hidden rounded-full border border-zinc-200 bg-white px-3 py-2 text-xs font-bold text-zinc-600 sm:inline-flex">
                            {{ auth()->user()?->email }}
                        </span>
                        <span class="rounded-full border border-vant-gold/40 bg-vant-gold/10 px-3 py-2 text-xs font-black uppercase tracking-wide text-vant-gold">
                            <span class="admin-live-dot mr-2"></span>Live
                        </span>
                        <a href="{{ route('home') }}" class="rounded-full border border-zinc-200 bg-white px-3 py-2 text-xs font-bold uppercase tracking-wide text-zinc-700 hover:border-vant-gold hover:text-vant-gold">Public Site</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-zinc-200 bg-white px-3 py-2 text-xs font-bold uppercase tracking-wide text-zinc-700 hover:border-vant-orange hover:text-vant-orange">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <nav class="admin-nav-strip sticky top-[73px] z-30 px-4 md:px-8">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-6 gap-y-0">
                    @foreach ($primaryNavLinks as [$label, $route, $activePattern, $icon, $description, $count, $permission])
                        <a href="{{ route($route) }}" title="{{ $description }}" class="admin-nav-link {{ request()->routeIs($activePattern) ? 'is-active' : '' }}">
                            <span class="admin-nav-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $adminIcon($icon) !!}</svg>
                            </span>
                            {{ $label }}
                        </a>
                    @endforeach
                    @if ($secondaryNavLinks->isNotEmpty())
                        <details class="admin-nav-more relative shrink-0" @if ($secondaryNavActive) open @endif>
                            <summary class="admin-nav-link cursor-pointer list-none {{ $secondaryNavActive ? 'is-active' : '' }}">
                                <span class="admin-nav-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $adminIcon('menu') !!}</svg>
                                </span>
                                More
                            </summary>
                            <div class="admin-nav-menu">
                                @foreach ($secondaryNavLinks as [$label, $route, $activePattern, $icon, $description, $count, $permission])
                                    <a href="{{ route($route) }}" title="{{ $description }}" class="{{ request()->routeIs($activePattern) ? 'is-active' : '' }}">
                                        <span class="admin-nav-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $adminIcon($icon) !!}</svg>
                                        </span>
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
            </nav>

            <main class="admin-content px-4 py-7 md:px-8">
                <div class="mx-auto max-w-7xl">
                    <x-alert />
                    @yield('admin')
                </div>
            </main>
        </div>

        @stack('scripts')
    </body>
</html>

