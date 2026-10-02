@extends('layouts.seo')

@section('title', 'Leivant Construction Projects in Tanzania')
@section('meta_description', 'See Leivant Construction Solutions project work in Dar es Salaam, Zanzibar, and Dodoma — real site photography, scope, and delivery approach.')
@section('canonical', route('projects.index'))

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap">

<style>
/* =========================================================
   LEIVANT PROJECTS
   Same tokens as the homepage. If both pages share a CSS file,
   move the "shared" block there and keep only the .proj-* rules.
   ========================================================= */

/* ---------- shared ---------- */
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
    --live: #2fa763;

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
.home-v2 :focus-visible { outline: 3px solid var(--amber); outline-offset: 3px; }

.home-v2 .ed-wrap { width: 100%; max-width: none; margin-inline: 0; padding-inline: var(--pad); }
.home-v2 .ed-section { padding: clamp(72px, 9vw, 128px) 0; }

.home-v2 .ed-lead { max-width: 85ch; color: var(--muted); font-size: 18px; line-height: 1.7; }
.home-v2 .ed-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 56px;
    padding: 0 28px;
    border-radius: var(--radius);
    font: 600 16px/1 var(--text);
    text-decoration: none;
    transition: background .2s ease, border-color .2s ease, color .2s ease;
}
.home-v2 .ed-btn-dark { background: var(--ink); border: 2px solid var(--ink); color: #fff; }
.home-v2 .ed-btn-dark:hover { background: var(--ink-2); }
.home-v2 .ed-btn-outline { background: transparent; border: 2px solid var(--ink); color: var(--ink); }
.home-v2 .ed-btn-outline:hover { background: var(--ink); color: #fff; }

.home-v2 svg { flex: 0 0 auto; }

/* ---------- HERO ---------- */
.home-v2 .proj-hero {
    position: relative;
    isolation: isolate;
    display: flex;
    align-items: flex-end;
    min-height: clamp(560px, 80vh, 780px);
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
    object-position: center 48%;
    filter: saturate(.85) contrast(1.05);
}
.home-v2 .proj-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background:
        linear-gradient(90deg, rgba(10,13,11,.9) 0%, rgba(10,13,11,.58) 48%, rgba(10,13,11,.12) 88%),
        linear-gradient(0deg, rgba(10,13,11,.88) 0%, transparent 55%);
}
.home-v2 .proj-hero-inner { width: 100%; padding-top: 140px; padding-bottom: clamp(36px, 5vw, 64px); }

.home-v2 .proj-hero-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 22px;
    color: var(--amber-light);
    font: 600 16px/1.3 var(--text);
}
.home-v2 .proj-hero-label svg { width: 20px; height: 20px; }

.home-v2 .proj-hero h1 {
    max-width: 18ch;
    margin: 0;
    font: 700 clamp(56px, 8.6vw, 128px)/.9 var(--display);
    letter-spacing: -.015em;
}
.home-v2 .proj-hero h1 span {
    display: block;
    margin-top: 18px;
    color: rgba(255,255,255,.78);
    font: 500 clamp(26px, 3vw, 42px)/1.08 var(--display);
    letter-spacing: 0;
}
.home-v2 .proj-hero .ed-lead { margin: 26px 0 0; max-width: 70ch; color: rgba(255,255,255,.84); font-size: 19px; }

.home-v2 .proj-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 0;
    margin: clamp(36px, 5vw, 64px) 0 0;
    padding: 0;
    border-top: 2px solid rgba(255,255,255,.28);
}
.home-v2 .proj-stats > div {
    min-width: 0;
    padding: 20px clamp(24px, 4vw, 64px) 0 0;
}
.home-v2 .proj-stats dt { font: 700 clamp(44px, 5vw, 64px)/1 var(--display); }
.home-v2 .proj-stats dd { margin: 6px 0 0; color: rgba(255,255,255,.72); font-size: 15px; }

/* ---------- JUMP NAV ---------- */
.home-v2 .proj-jump {
    position: sticky;
    top: var(--sticky-top);
    z-index: 20;
    background: var(--ink);
    border-bottom: 1px solid rgba(255,255,255,.12);
}
.home-v2 .proj-jump-inner {
    display: flex;
    gap: clamp(20px, 3vw, 44px);
    padding: 0 var(--pad);
    overflow-x: auto;
    scrollbar-width: none;
}
.home-v2 .proj-jump-inner::-webkit-scrollbar { display: none; }
.home-v2 .proj-jump a {
    flex: 0 0 auto;
    padding: 18px 0 15px;
    border-bottom: 3px solid transparent;
    color: rgba(255,255,255,.78);
    font: 600 15px/1 var(--text);
    text-decoration: none;
    white-space: nowrap;
    transition: color .2s ease, border-color .2s ease;
}
.home-v2 .proj-jump a:hover { color: #fff; border-bottom-color: var(--amber); }

/* ---------- PROJECT BLOCKS ---------- */
.home-v2 .proj-block { padding: clamp(64px, 8vw, 112px) 0; scroll-margin-top: calc(var(--sticky-top) + 56px); }
.home-v2 .proj-block + .proj-block { border-top: 1px solid var(--line); }
.home-v2 .proj-block.alt { background: var(--concrete); }

.home-v2 .proj-grid {
    display: grid;
    grid-template-columns: minmax(0, .8fr) minmax(0, 1.2fr);
    gap: clamp(32px, 5vw, 88px);
    align-items: start;
}
.home-v2 .proj-grid.flip { grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr); }
.home-v2 .proj-grid.flip .proj-copy { order: 2; }
.home-v2 .proj-grid.flip .proj-media { order: 1; }

.home-v2 .proj-copy { min-width: 0; position: sticky; top: calc(var(--sticky-top) + 88px); }

.home-v2 .proj-num {
    margin: 0 0 8px;
    color: var(--amber);
    font: 700 clamp(56px, 6vw, 88px)/.9 var(--display);
}

.home-v2 .proj-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
.home-v2 .proj-pill-type,
.home-v2 .proj-pill-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    font: 600 14px/1 var(--text);
}
.home-v2 .proj-pill-type { background: var(--ink); color: #fff; }
.home-v2 .proj-pill-status { background: transparent; border: 1px solid var(--line); color: var(--ink); }
.home-v2 .proj-block.alt .proj-pill-status { background: rgba(255,255,255,.55); }
.home-v2 .proj-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--live); box-shadow: 0 0 0 3px rgba(47,167,99,.25); }

.home-v2 .proj-copy h2 {
    margin: 0 0 14px;
    font: 700 clamp(40px, 4.6vw, 68px)/.95 var(--display);
    letter-spacing: -.01em;
}
.home-v2 .proj-location {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 20px;
    color: var(--amber-deep);
    font: 600 17px/1.3 var(--text);
}
.home-v2 .proj-location svg { width: 20px; height: 20px; }
.home-v2 .proj-summary { max-width: 62ch; margin: 0 0 28px; color: var(--muted); font-size: 18px; line-height: 1.7; }

.home-v2 .proj-details { list-style: none; margin: 0 0 32px; padding: 0; border-top: 2px solid var(--ink); }
.home-v2 .proj-details li {
    display: grid;
    grid-template-columns: 44px minmax(0, 1fr);
    gap: 12px;
    padding: 16px 0;
    border-bottom: 1px solid var(--line);
}
.home-v2 .proj-details .n { color: var(--amber-deep); font: 700 22px/1.2 var(--display); }
.home-v2 .proj-details p { margin: 0; font-size: 16px; line-height: 1.55; }

.home-v2 .proj-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 54px;
    padding: 0 26px;
    border-radius: var(--radius);
    background: var(--ink);
    color: #fff;
    font: 600 16px/1 var(--text);
    text-decoration: none;
    transition: background .2s ease;
}
.home-v2 .proj-cta:hover { background: var(--amber); color: #15130c; }

/* ---------- MEDIA ---------- */
.home-v2 .proj-media { min-width: 0; }

.home-v2 .proj-hero-shot,
.home-v2 .proj-thumb {
    position: relative;
    display: block;
    width: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background: #171a18;
    border: 0;
    border-radius: var(--radius);
    cursor: zoom-in;
}
.home-v2 .proj-hero-shot { aspect-ratio: 16 / 11; }
.home-v2 .proj-hero-shot img,
.home-v2 .proj-thumb img {
    display: block;
    width: 100%;
    height: 100%;
    max-width: none;
    object-fit: cover;
    transition: transform .6s ease, opacity .2s ease;
}
.home-v2 .proj-hero-shot:hover img { transform: scale(1.03); }

.home-v2 .proj-hero-shot .tag {
    position: absolute;
    top: 14px;
    left: 14px;
    padding: 8px 14px;
    border-radius: var(--radius);
    background: var(--ink);
    color: #fff;
    font: 600 14px/1 var(--text);
}
.home-v2 .proj-hero-shot .zoom {
    position: absolute;
    right: 14px;
    bottom: 14px;
    padding: 9px 14px;
    border-radius: var(--radius);
    background: rgba(255,255,255,.94);
    color: var(--ink);
    font: 600 14px/1 var(--text);
    opacity: 0;
    transform: translateY(4px);
    transition: opacity .2s ease, transform .2s ease;
}
.home-v2 .proj-hero-shot:hover .zoom,
.home-v2 .proj-hero-shot:focus-visible .zoom { opacity: 1; transform: none; }

.home-v2 .proj-thumbs { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 10px; }
.home-v2 .proj-thumb { aspect-ratio: 1 / 1; }
.home-v2 .proj-thumb:hover img { opacity: .82; }

/* ---------- CLOSING CTA ---------- */
.home-v2 .ed-cta {
    background: var(--concrete);
    border-top: 1px solid var(--line);
}
.home-v2 .ed-cta h2 {
    max-width: 22ch;
    margin: 0 0 20px;
    font: 700 clamp(44px, 6vw, 88px)/.92 var(--display);
    letter-spacing: -.01em;
}
.home-v2 .ed-cta .ed-lead { margin: 0; }
.home-v2 .ed-contact-row { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 32px; }

/* ---------- LIGHTBOX ---------- */
.proj-lightbox {
    width: 100%;
    max-width: none;
    height: 100%;
    max-height: none;
    margin: 0;
    padding: 0;
    border: 0;
    background: transparent;
}
.proj-lightbox::backdrop { background: rgba(10, 13, 11, .94); }
.proj-lightbox[open] { display: grid; place-items: center; }
.proj-lightbox-wrap { position: relative; display: grid; place-items: center; width: 100%; height: 100%; padding: 56px 16px 24px; }
.proj-lightbox img {
    display: block;
    max-width: min(96vw, 1600px);
    max-height: calc(100vh - 96px);
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 3px;
}
.proj-lightbox-close {
    position: absolute;
    top: 12px;
    right: 12px;
    display: grid;
    place-items: center;
    width: 48px;
    height: 48px;
    border: 1px solid rgba(255,255,255,.4);
    border-radius: 50%;
    background: rgba(255,255,255,.08);
    color: #fff;
    font-size: 18px;
    cursor: pointer;
}
.proj-lightbox-close:hover { background: rgba(255,255,255,.2); }
.proj-lightbox-close:focus-visible { outline: 3px solid #c7922e; outline-offset: 3px; }

/* ---------- responsive ---------- */
@media (max-width: 1000px) {
    .home-v2 .proj-grid,
    .home-v2 .proj-grid.flip { grid-template-columns: 1fr; }
    .home-v2 .proj-grid.flip .proj-copy,
    .home-v2 .proj-grid.flip .proj-media { order: 0; }
    .home-v2 .proj-copy { position: static; }
}

@media (max-width: 680px) {
    .home-v2 .proj-hero { min-height: 0; }
    .home-v2 .proj-hero-inner { padding-top: 110px; }
    .home-v2 .proj-hero h1 { font-size: clamp(52px, 16vw, 80px); }
    .home-v2 .proj-hero .ed-lead { font-size: 17px; }
    .home-v2 .proj-stats > div { padding-right: 28px; }
    .home-v2 .proj-thumbs { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .home-v2 .proj-hero-shot { aspect-ratio: 4 / 3; }
    .home-v2 .proj-hero-shot .zoom { opacity: 1; transform: none; }
    .home-v2 .proj-cta,
    .home-v2 .ed-contact-row .ed-btn { width: 100%; }
}

@media (hover: none) {
    .home-v2 .proj-hero-shot .zoom { opacity: 1; transform: none; }
}

@media (prefers-reduced-motion: reduce) {
    .home-v2 *,
    .home-v2 *::before,
    .home-v2 *::after { scroll-behavior: auto !important; transition: none !important; animation: none !important; }
}
</style>

    @php
        $projects = [
            [
                'slug' => 'kinyerezi',
                'name' => 'Kinyerezi Residential Development',
                'location' => 'Kinyerezi, Dar es Salaam',
                'region' => 'Dar es Salaam',
                'type' => 'Residential construction',
                'status' => 'Active site delivery',
                'active' => true,
                'hero' => 'leivant-webp/20260523_141609.jpg.webp',
                'summary' => 'A ground-up residential build in Kinyerezi where Leivant managed planning coordination, foundation works, structural progress, and supervised finishing under real Dar es Salaam site conditions.',
                'details' => [
                    'Site preparation, setting out, and foundation execution with daily progress checks.',
                    'Structural frame, blockwork, roofing, and supervised interior finishing stages.',
                    'Materials coordination and equipment scheduling aligned to access and weather windows.',
                    'One accountable Leivant desk from brief review through practical handover.',
                ],
                'photos' => [
                    'leivant-webp/20260608_162620.jpg.webp',
                    'leivant-webp/20260608_162631.jpg.webp',
                    'leivant-webp/20260523_141714.jpg.webp',
                    'leivant-webp/20260608_162632.jpg.webp',
                ],
            ],
            [
                'slug' => 'zanzibar',
                'name' => 'Zanzibar Coastal Build',
                'location' => 'Zanzibar',
                'region' => 'Zanzibar',
                'type' => 'Coastal construction',
                'status' => 'Delivered with site supervision',
                'active' => false,
                'hero' => 'leivant-webp/20260523_142319.jpg.webp',
                'summary' => 'Construction work on Zanzibar where Leivant handled site logistics, material movement, structural progress, and quality control across island access and coastal build conditions.',
                'details' => [
                    'Pre-build site review covering access routes, material delivery timing, and storage.',
                    'Supervised structural and finishing work with practical reporting to the client.',
                    'Equipment and labour coordination adapted to island logistics and coastal exposure.',
                    'Clear milestone follow-up from mobilization through completion.',
                ],
                'photos' => [
                    'leivant-webp/20260523_142333.jpg.webp',
                    'leivant-webp/IMG-20260529-WA0049.jpg.webp',
                    'leivant-webp/IMG-20260529-WA0066.jpg.webp',
                    'leivant-webp/20260523_142319.jpg.webp',
                ],
            ],
            [
                'slug' => 'dodoma',
                'name' => 'Dodoma Structural Works',
                'location' => 'Dodoma, Tanzania',
                'region' => 'Dodoma',
                'type' => 'Residential construction',
                'status' => 'Active site delivery',
                'active' => true,
                'hero' => 'leivant-webp/dodoma-progress-hero.webp',
                'summary' => 'An in-progress residential build in Dodoma where Leivant is supervising slab formwork, reinforcement steel fixing, and staircase construction — engineers on the deck checking levels, alignment, and readiness before each concrete pour.',
                'details' => [
                    'Supervised slab formwork and rebar fixing with pour-readiness checks on every panel.',
                    'Staircase setting out, steel placement, and propping inspected before casting.',
                    'Daily site presence: drawings review, measurements, and progress reporting to the client.',
                    'One accountable Leivant desk from foundation through walling and roofing stages.',
                ],
                'photos' => [
                    'leivant-webp/dodoma-progress-01.webp',
                    'leivant-webp/dodoma-progress-02.webp',
                    'leivant-webp/dodoma-progress-03.webp',
                    'leivant-webp/dodoma-progress-04.webp',
                ],
            ],
            [
                'slug' => 'mapacha',
                'name' => 'Mapacha wa 3 Complex',
                'location' => 'Dar es Salaam, Tanzania',
                'region' => 'Dar es Salaam',
                'type' => 'Commercial construction',
                'status' => 'Delivered complete',
                'active' => false,
                'hero' => 'leivant-webp/mapacha-complex-hero.webp',
                'summary' => 'A completed commercial complex in Dar es Salaam — glass facade, finished interiors, marble staircases, and handed-over retail and office spaces, delivered complete by the Leivant team.',
                'details' => [
                    'Full structural delivery from foundation through frame, facade, and roofing.',
                    'Glass curtain walls, aluminium detailing, and finished interior fit-outs.',
                    'Marble staircases, stainless balustrades, and site-wide finishing works.',
                    'Snagging, handover documentation, and a completed complex ready for tenants.',
                ],
                'photos' => [
                    'leivant-webp/mapacha-complex-01.webp',
                    'leivant-webp/mapacha-complex-02.webp',
                    'leivant-webp/mapacha-complex-03.webp',
                ],
            ],
        ];

        $activeCount = collect($projects)->where('active', true)->count();
        $regionCount = collect($projects)->pluck('region')->unique()->count();
    @endphp

    <div class="home-v2">

        {{-- HERO --}}
        <section class="proj-hero" aria-label="Projects introduction">
            <img src="{{ asset($projects[0]['hero']) }}" alt="" decoding="async" fetchpriority="high">
            <div class="ed-wrap proj-hero-inner">
                <p class="proj-hero-label" data-reveal><x-nav-icon name="projects" /> Our projects</p>
                <h1 data-reveal>Real Leivant construction work <span>in Tanzania</span></h1>
                <p class="ed-lead" data-reveal>
                    Photography and project notes from active and completed sites — not stock images or generic categories. These are four projects Leivant has planned, built, and supervised on the ground.
                </p>
                <dl class="proj-stats" data-reveal>
                    @foreach ([[count($projects), 'Featured projects'], [$regionCount, 'Regions'], [$activeCount, 'Active sites']] as [$n, $l])
                        <div>
                            <dt>{{ $n }}</dt>
                            <dd>{{ $l }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- JUMP NAV --}}
        <nav aria-label="Projects" class="proj-jump">
            <div class="proj-jump-inner">
                @foreach ($projects as $project)
                    <a href="#{{ $project['slug'] }}">{{ $project['region'] }}</a>
                @endforeach
            </div>
        </nav>

        {{-- PROJECTS --}}
        @foreach ($projects as $index => $project)
            <section id="{{ $project['slug'] }}" class="proj-block{{ $index % 2 === 1 ? ' alt' : '' }}" aria-label="{{ $project['name'] }}">
                <div class="ed-wrap proj-grid{{ $index % 2 === 1 ? ' flip' : '' }}">

                    <div class="proj-copy" data-reveal>
                        <p class="proj-num" aria-hidden="true">{{ sprintf('%02d', $index + 1) }}</p>
                        <div class="proj-pills">
                            <span class="proj-pill-type">{{ $project['type'] }}</span>
                            <span class="proj-pill-status">
                                @if ($project['active'])
                                    <span class="proj-dot" aria-hidden="true"></span>
                                @endif
                                {{ $project['status'] }}
                            </span>
                        </div>
                        <h2>{{ $project['name'] }}</h2>
                        <p class="proj-location"><x-nav-icon name="map" /> {{ $project['location'] }}</p>
                        <p class="proj-summary">{{ $project['summary'] }}</p>
                        <ol class="proj-details">
                            @foreach ($project['details'] as $i => $detail)
                                <li>
                                    <span class="n">{{ sprintf('%02d', $i + 1) }}</span>
                                    <p>{{ $detail }}</p>
                                </li>
                            @endforeach
                        </ol>
                        <a href="{{ route('contact.index') }}" class="proj-cta">Discuss a similar project</a>
                    </div>

                    <div class="proj-media" data-reveal>
                        <button type="button" class="proj-hero-shot" data-lightbox
                                data-src="{{ asset($project['hero']) }}"
                                data-alt="{{ $project['name'] }} — Leivant construction site in {{ $project['region'] }}"
                                aria-label="Enlarge photo: {{ $project['name'] }}">
                            <img src="{{ asset($project['hero']) }}"
                                 alt="{{ $project['name'] }} — Leivant construction site in {{ $project['region'] }}"
                                 loading="lazy" decoding="async">
                            <span class="tag">{{ $project['region'] }}</span>
                            <span class="zoom">Enlarge</span>
                        </button>
                        <div class="proj-thumbs">
                            @foreach ($project['photos'] as $photo)
                                <button type="button" class="proj-thumb" data-lightbox
                                        data-src="{{ asset($photo) }}"
                                        data-alt="{{ $project['name'] }} site progress — Leivant Construction"
                                        aria-label="Enlarge site photo">
                                    <img src="{{ asset($photo) }}"
                                         alt="{{ $project['name'] }} site progress — Leivant Construction"
                                         loading="lazy" decoding="async">
                                </button>
                            @endforeach
                        </div>
                    </div>

                </div>
            </section>
        @endforeach

        {{-- CLOSING BAND --}}
        <section class="ed-section ed-cta" aria-label="Start a project">
            <div class="ed-wrap" data-reveal>
                <h2>Planning a project in Dar es Salaam, Zanzibar or Dodoma?</h2>
                <p class="ed-lead">Send your brief with location, drawings, and timeline. Leivant reviews the site reality and responds with a practical next step.</p>
                <div class="ed-contact-row">
                    <a href="{{ route('contact.index') }}" class="ed-btn ed-btn-dark">Send project brief</a>
                    <a href="tel:+255717970799" class="ed-btn ed-btn-outline">Call 0717 970 799</a>
                </div>
            </div>
        </section>

    </div>

    {{-- LIGHTBOX (native dialog, no libraries) --}}
    <dialog id="lightbox" class="proj-lightbox" aria-label="Photo viewer">
        <div class="proj-lightbox-wrap">
            <img id="lightbox-img" src="" alt="">
            <button type="button" id="lightbox-close" class="proj-lightbox-close" aria-label="Close">✕</button>
        </div>
    </dialog>
@endsection

@push('scripts')
    <script>
        (function () {
            var dialog = document.getElementById('lightbox');
            var image = document.getElementById('lightbox-img');
            if (!dialog || !image || typeof dialog.showModal !== 'function') return;
            document.querySelectorAll('[data-lightbox]').forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    image.src = trigger.getAttribute('data-src');
                    image.alt = trigger.getAttribute('data-alt') || '';
                    dialog.showModal();
                });
            });
            document.getElementById('lightbox-close').addEventListener('click', function () {
                dialog.close();
            });
            dialog.addEventListener('click', function (event) {
                if (event.target === dialog) dialog.close();
            });
        })();
    </script>
@endpush