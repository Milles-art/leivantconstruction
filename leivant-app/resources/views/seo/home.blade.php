@extends('layouts.seo')

@section('title', 'Leivant Construction Solutions | Dar es Salaam, Tanzania')
@section('canonical', route('home'))
@section('meta_description', 'Leivant Construction Solutions plans, builds, renovates, and supervises construction projects across Tanzania. BRELA 626936. Kipawa, Dar es Salaam to Zanzibar.')
@section('og_image', asset('leivant-webp/20260523_141609.jpg.webp'))

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap">

<style>
/* =========================================================
   LEIVANT HOME
   Type: Barlow Condensed (site-signage headings) + Barlow (text)
   Palette: site ink, concrete, paper, safety amber
   ========================================================= */
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
    --max: none;
    --radius: 4px;

    width: 100%;
    background: var(--paper);
    color: var(--ink);
    font-family: var(--text);
    overflow-x: clip;
}

.home-v2 img,
.home-v2 video,
.home-v2 iframe { max-width: 100%; }

.home-v2 a { color: inherit; }

.home-v2 :focus-visible {
    outline: 3px solid var(--amber);
    outline-offset: 3px;
}

/* ---------- shared layout ---------- */
.home-v2 .ed-wrap {
    width: 100%;
    max-width: none;
    margin-inline: 0;
    padding-inline: var(--pad);
}

.home-v2 .ed-section { padding: clamp(72px, 9vw, 128px) 0; }
.home-v2 .ed-section + .ed-section { border-top: 1px solid var(--line); }
.home-v2 .ed-concrete { background: var(--concrete); }

.home-v2 .ed-h2 {
    max-width: 30ch;
    margin: 0 0 22px;
    font: 700 clamp(40px, 5.4vw, 76px)/.95 var(--display);
    letter-spacing: -.01em;
}

.home-v2 .ed-lead {
    max-width: 85ch;
    color: var(--muted);
    font-size: 18px;
    line-height: 1.7;
}
.home-v2 .ed-lead p { margin: 0 0 16px; }
.home-v2 .ed-lead p:last-child { margin-bottom: 0; }

.home-v2 .ed-head {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 24px clamp(32px, 6vw, 96px);
    align-items: end;
    margin-bottom: clamp(36px, 5vw, 64px);
}
.home-v2 .ed-head .ed-h2 { margin: 0; }
.home-v2 .ed-head .ed-lead { margin: 0; }

/* text link with amber rule */
.home-v2 .ed-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font: 600 15px/1.2 var(--text);
    text-decoration: none;
    padding: 6px 0;
}
.home-v2 .ed-link::after {
    content: "";
    width: 28px;
    height: 2px;
    background: var(--amber);
    transition: width .2s ease;
}
.home-v2 .ed-link:hover::after { width: 48px; }
.home-v2 .ed-more { margin: 36px 0 0; }

/* buttons */
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
.home-v2 .ed-btn-solid { background: var(--amber); border: 1px solid var(--amber); color: #15130c; }
.home-v2 .ed-btn-solid:hover { background: var(--amber-light); border-color: var(--amber-light); }
.home-v2 .ed-btn-ghost { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.45); color: #fff; }
.home-v2 .ed-btn-ghost:hover { background: rgba(255,255,255,.16); }
.home-v2 .ed-btn-dark { background: var(--ink); border: 1px solid var(--ink); color: #fff; }
.home-v2 .ed-btn-dark:hover { background: var(--ink-2); }

/* ---------- HERO ---------- */
.home-v2 .ed-hero {
    position: relative;
    isolation: isolate;
    display: flex;
    align-items: flex-end;
    min-height: clamp(660px, 92vh, 900px);
    background: #111;
    color: #fff;
}
.home-v2 .ed-hero picture,
.home-v2 .ed-hero picture img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}
.home-v2 .ed-hero picture { z-index: -2; }
.home-v2 .ed-hero picture img {
    object-fit: cover;
    object-position: center 48%;
    filter: saturate(.85) contrast(1.05);
}
.home-v2 .ed-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background:
        linear-gradient(90deg, rgba(10,13,11,.9) 0%, rgba(10,13,11,.6) 46%, rgba(10,13,11,.12) 85%),
        linear-gradient(0deg, rgba(10,13,11,.88) 0%, transparent 52%);
}
.home-v2 .ed-hero-inner { width: 100%; padding-top: 150px; }

.home-v2 .ed-hero-reg {
    margin: 0 0 22px;
    color: var(--amber-light);
    font: 600 16px/1.3 var(--text);
}

.home-v2 .ed-hero h1 {
    max-width: 20ch;
    margin: 0;
    font: 700 clamp(60px, 9.4vw, 138px)/.88 var(--display);
    letter-spacing: -.015em;
}
.home-v2 .ed-hero h1 span {
    display: block;
    max-width: 40ch;
    margin-top: 22px;
    color: rgba(255,255,255,.78);
    font: 500 clamp(26px, 3vw, 42px)/1.08 var(--display);
    letter-spacing: 0;
}

.home-v2 .ed-hero .ed-lead {
    max-width: 75ch;
    margin: 28px 0 0;
    color: rgba(255,255,255,.84);
    font-size: 19px;
}

.home-v2 .ed-hero-cta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 32px; }

/* The memorable element: the project lifecycle as a site programme line */
.home-v2 .ed-stages {
    list-style: none;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0;
    margin: clamp(48px, 7vw, 88px) 0 0;
    padding: 0 0 clamp(28px, 4vw, 48px);
}
.home-v2 .ed-stages li {
    position: relative;
    padding: 26px 24px 0 0;
    border-top: 2px solid rgba(255,255,255,.28);
}
.home-v2 .ed-stages li::before {
    content: "";
    position: absolute;
    top: -8px;
    left: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--amber);
    box-shadow: 0 0 0 4px rgba(199,146,46,.28);
}
.home-v2 .ed-stages li:last-child { border-top-color: var(--amber); }
.home-v2 .ed-stages b {
    display: block;
    margin-bottom: 6px;
    font: 700 24px/1.05 var(--display);
}
.home-v2 .ed-stages span {
    display: block;
    max-width: 26ch;
    color: rgba(255,255,255,.7);
    font-size: 14px;
    line-height: 1.5;
}

/* ---------- ABOUT ---------- */
.home-v2 .ed-about-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(320px, .5fr);
    gap: clamp(40px, 6vw, 96px);
    align-items: start;
}
.home-v2 .ed-about-grid .ed-h2 { max-width: 24ch; }
.home-v2 .ed-facts {
    list-style: none;
    margin: 8px 0 0;
    padding: 0;
    border-top: 2px solid var(--ink);
}
.home-v2 .ed-facts li {
    padding: 22px 0;
    border-bottom: 1px solid var(--line);
}
.home-v2 .ed-facts strong {
    display: block;
    font: 700 40px/1 var(--display);
}
.home-v2 .ed-facts span {
    display: block;
    margin-top: 6px;
    color: var(--muted);
    font-size: 15px;
    line-height: 1.4;
}

/* ---------- SERVICES ---------- */
.home-v2 .ed-service-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
    min-width: 0;
}
.home-v2 .ed-service {
    display: grid;
    grid-template-columns: 42% 58%;
    min-width: 0;
    min-height: 340px;
    overflow: hidden;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
}
.home-v2 .ed-service-media { position: relative; min-height: 100%; overflow: hidden; }
.home-v2 .ed-service-media img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    max-width: none;
    object-fit: cover;
    transition: transform .6s ease;
}
.home-v2 .ed-service:hover .ed-service-media img { transform: scale(1.04); }
.home-v2 .ed-service-body { display: flex; flex-direction: column; min-width: 0; padding: 30px 28px; }
.home-v2 .ed-service-body h3 { margin: 0 0 12px; font: 700 32px/1 var(--display); }
.home-v2 .ed-service-body p { margin: 0; color: var(--muted); font-size: 16px; line-height: 1.65; }
.home-v2 .ed-service-body .ed-link { margin-top: auto; padding-top: 22px; }

/* ---------- WHY ---------- */
.home-v2 .ed-why { background: var(--ink); color: #fff; }
.home-v2 .ed-why .ed-h2 { max-width: 30ch; color: #fff; }
.home-v2 .ed-why-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0 clamp(24px, 3vw, 44px);
    margin-top: clamp(40px, 6vw, 72px);
}
.home-v2 .ed-why-grid article { min-width: 0; padding-top: 24px; border-top: 3px solid var(--amber); }
.home-v2 .ed-why-grid h3 { margin: 0 0 12px; font: 700 28px/1 var(--display); }
.home-v2 .ed-why-grid p { margin: 0; color: rgba(255,255,255,.72); font-size: 16px; line-height: 1.65; }

/* ---------- PROJECTS ---------- */
.home-v2 .ed-project-grid {
    display: grid;
    grid-template-columns: 1.3fr 1fr 1fr;
    gap: 16px;
    min-width: 0;
}
.home-v2 .ed-project {
    position: relative;
    min-width: 0;
    min-height: 480px;
    overflow: hidden;
    background: #171a18;
    border-radius: var(--radius);
    color: #fff;
    text-decoration: none;
}
.home-v2 .ed-project img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    max-width: none;
    object-fit: cover;
    transition: transform .6s ease;
}
.home-v2 .ed-project:hover img { transform: scale(1.04); }
.home-v2 .ed-project::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 38%, rgba(8,10,9,.9));
}
.home-v2 .ed-project-cap { position: absolute; right: 22px; bottom: 22px; left: 22px; z-index: 1; }
.home-v2 .ed-project-cap span { display: block; margin-bottom: 6px; color: var(--amber-light); font: 600 15px/1.2 var(--text); }
.home-v2 .ed-project-cap strong { display: block; font: 700 28px/1.02 var(--display); }

/* site diary */
.home-v2 .ed-diary { margin-top: clamp(72px, 9vw, 120px); padding-top: clamp(48px, 6vw, 80px); border-top: 2px solid var(--ink); }
.home-v2 .ed-diary-rail {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(280px, 24%);
    gap: 14px;
    margin-top: 36px;
    padding-bottom: 12px;
    overflow-x: auto;
    scroll-snap-type: x proximity;
    overscroll-behavior-inline: contain;
    scrollbar-width: thin;
}
.home-v2 .ed-diary-rail figure { margin: 0; min-width: 0; scroll-snap-align: start; }
.home-v2 .ed-diary-rail img {
    display: block;
    width: 100%;
    max-width: none;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    border-radius: var(--radius);
}
.home-v2 .ed-diary-rail figcaption { padding-top: 10px; color: var(--muted); font-size: 15px; font-weight: 500; }

/* ---------- LIVE CATALOGUE / PROVIDERS ---------- */
.home-v2 #home-product-grid,
.home-v2 #home-provider-grid { margin-top: 8px; }
.home-v2 .product-grid-compact { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; min-width: 0; }
.home-v2 .provider-grid-compact { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; min-width: 0; }
.home-v2 .product-grid-compact > *,
.home-v2 .provider-grid-compact > * { min-width: 0; }
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

.home-v2 #home-catalogue-count { color: var(--muted); font-size: 15px; }
.home-v2 .loading {
    grid-column: 1 / -1;
    display: grid;
    place-items: center;
    min-height: 120px;
    margin: 0;
    padding: 20px;
    border: 1px dashed var(--line);
    border-radius: var(--radius);
    color: var(--muted);
    font-size: 15px;
}

/* ---------- TRUST ---------- */
.home-v2 .ed-trust-grid {
    display: grid;
    grid-template-columns: minmax(0, .8fr) minmax(0, 1.2fr);
    gap: clamp(36px, 7vw, 110px);
    align-items: start;
}
.home-v2 .ed-trust { list-style: none; margin: 0; padding: 0; border-top: 2px solid var(--ink); }
.home-v2 .ed-trust li { display: grid; grid-template-columns: 34px minmax(0, 1fr); gap: 16px; padding: 22px 0; border-bottom: 1px solid var(--line); }
.home-v2 .ed-trust .tick {
    display: grid;
    place-items: center;
    width: 28px;
    height: 28px;
    margin-top: 2px;
    border-radius: 50%;
    background: var(--amber);
    color: #15130c;
    font-size: 14px;
    font-weight: 800;
}
.home-v2 .ed-trust strong { display: block; margin-bottom: 4px; font: 700 26px/1.05 var(--display); }
.home-v2 .ed-trust p { margin: 0; color: var(--muted); font-size: 16px; line-height: 1.55; }

/* ---------- CTA ---------- */
.home-v2 .ed-cta { background: var(--concrete); }
.home-v2 .ed-cta-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr);
    gap: clamp(36px, 6vw, 96px);
    align-items: center;
}
.home-v2 .ed-cta h2 { max-width: 22ch; margin: 0 0 20px; font: 700 clamp(46px, 6vw, 88px)/.92 var(--display); letter-spacing: -.01em; }
.home-v2 .ed-cta .ed-lead { margin: 0; }
.home-v2 .ed-cta-note { margin: 18px 0 28px; font-weight: 600; max-width: 70ch; line-height: 1.5; }
.home-v2 .ed-contact-card { background: var(--ink); color: #fff; border-radius: var(--radius); padding: clamp(24px, 3vw, 40px); }
.home-v2 .ed-contact-card p { margin: 0 0 6px; color: rgba(255,255,255,.7); font-size: 15px; }
.home-v2 .ed-contact-phone { display: block; margin-bottom: 22px; font: 700 clamp(36px, 4vw, 52px)/1 var(--display); text-decoration: none; }
.home-v2 .ed-contact-phone:hover { color: var(--amber-light); }
.home-v2 .ed-contact-row { display: grid; gap: 10px; }
.home-v2 .ed-contact-row a {
    display: flex;
    align-items: center;
    min-height: 50px;
    padding: 0 16px;
    border: 1px solid rgba(255,255,255,.28);
    border-radius: var(--radius);
    font-size: 15px;
    font-weight: 500;
    text-decoration: none;
    overflow-wrap: anywhere;
}
.home-v2 .ed-contact-row a:hover { border-color: var(--amber); background: rgba(255,255,255,.07); }

/* ---------- mobile call bar ---------- */
.home-v2 .ed-dock { display: none; }

/* ---------- responsive ---------- */
@media (max-width: 1100px) {
    .home-v2 .ed-project-grid { grid-template-columns: 1fr 1fr; }
    .home-v2 .ed-project:first-child { grid-column: 1 / -1; min-height: 420px; }
    .home-v2 .product-grid-compact { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .home-v2 .ed-diary-rail { grid-auto-columns: minmax(260px, 42%); }
}

@media (max-width: 980px) {
    .home-v2 .ed-head,
    .home-v2 .ed-about-grid,
    .home-v2 .ed-trust-grid,
    .home-v2 .ed-cta-grid { grid-template-columns: 1fr; }
    .home-v2 .ed-head { align-items: start; }
    .home-v2 .ed-service-grid { grid-template-columns: 1fr; }
    .home-v2 .ed-why-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 40px; }
    .home-v2 .provider-grid-compact { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .home-v2 .ed-stages { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 28px; }
    .home-v2 .ed-facts { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: 28px; }
}

@media (max-width: 680px) {
    .home-v2 { padding-bottom: 68px; }
    .home-v2 .ed-hero { min-height: 0; }
    .home-v2 .ed-hero-inner { padding-top: 110px; }
    .home-v2 .ed-hero h1 { font-size: clamp(56px, 17vw, 84px); }
    .home-v2 .ed-hero .ed-lead { font-size: 17px; }
    .home-v2 .ed-hero-cta .ed-btn { width: 100%; }
    .home-v2 .ed-stages { margin-top: 44px; }
    .home-v2 .ed-service { grid-template-columns: 1fr; min-height: 0; }
    .home-v2 .ed-service-media { height: 240px; min-height: 0; }
    .home-v2 .ed-service-body { padding: 24px 20px; }
    .home-v2 .ed-why-grid { grid-template-columns: 1fr; }
    .home-v2 .ed-project-grid { grid-template-columns: 1fr; }
    .home-v2 .ed-project,
    .home-v2 .ed-project:first-child { grid-column: auto; min-height: 360px; }
    .home-v2 .product-grid-compact,
    .home-v2 .provider-grid-compact { grid-template-columns: 1fr; }
    .home-v2 .ed-diary-rail { grid-auto-columns: 78%; }
    .home-v2 .ed-facts { grid-template-columns: 1fr; }

    .home-v2 .ed-dock {
        position: fixed;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 50;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 10px 14px calc(10px + env(safe-area-inset-bottom, 0px));
        background: rgba(21,24,22,.96);
        border-top: 1px solid rgba(255,255,255,.14);
    }
    .home-v2 .ed-dock a {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        border-radius: var(--radius);
        font: 600 16px/1 var(--text);
        text-decoration: none;
    }
    .home-v2 .ed-dock .call { background: var(--amber); color: #15130c; }
    .home-v2 .ed-dock .wa { background: #166534; color: #fff; }
}

@media (max-width: 420px) {
    .home-v2 .ed-stages { grid-template-columns: 1fr; }
    .home-v2 .ed-h2 { font-size: 40px; }
}

@media (prefers-reduced-motion: reduce) {
    .home-v2 *,
    .home-v2 *::before,
    .home-v2 *::after {
        scroll-behavior: auto !important;
        transition: none !important;
        animation: none !important;
    }
}
</style>


<div class="home-v2">

    {{-- HERO --}}
    <section class="ed-hero" aria-label="Introduction">
        <picture aria-hidden="true">
            <source media="(max-width: 760px)" srcset="{{ asset('leivant-webp/hero-mobile-800.webp') }}">
            <img src="{{ asset('leivant-webp/hero-1400.webp') }}" alt="" width="1400" height="1050" fetchpriority="high" decoding="async">
        </picture>
        <div class="ed-wrap ed-hero-inner">
            <p class="ed-hero-reg" data-reveal>Dar es Salaam, Tanzania &middot; BRELA 626936</p>
            <h1 data-reveal>One team. Every stage. <span>Your construction project planned, built, and delivered.</span></h1>
            <p class="ed-lead" data-reveal>
                Leivant manages the full lifecycle of construction projects across Tanzania — brief and BOQ review, materials, labour, equipment, and supervised site delivery, all under one roof.
            </p>
            <div class="ed-hero-cta" data-reveal>
                <a href="{{ route('contact.index') }}" class="ed-btn ed-btn-solid">Start a project brief</a>
                <a href="{{ route('services.index') }}" class="ed-btn ed-btn-ghost">View our services</a>
            </div>

            <ol class="ed-stages" aria-label="How a Leivant project runs" data-reveal>
                <li><b>Brief &amp; drawings</b><span>We review your brief, drawings, and site conditions.</span></li>
                <li><b>BOQ &amp; costing</b><span>Quantities and costs are agreed before ground breaks.</span></li>
                <li><b>Materials &amp; labour</b><span>Supply, crews, and equipment scheduled to your timeline.</span></li>
                <li><b>Supervised build</b><span>Our team supervises on site through to client handover.</span></li>
            </ol>
        </div>
    </section>

    {{-- ABOUT --}}
    <section class="ed-section" aria-label="About Leivant">
        <div class="ed-wrap ed-about-grid">
            <div data-reveal>
                <h2 class="ed-h2">Built to deliver, not just advise.</h2>
                <div class="ed-lead">
                    <p>Leivant Construction Solutions is a registered Tanzanian construction company with over a decade of active site experience. We work across residential, commercial, and renovation projects — coordinating every moving part so our clients don't have to chase multiple contractors, suppliers, and supervisors separately.</p>
                    <p>When you work with Leivant, you get one point of contact for drawings review, cost estimation, material sourcing, equipment logistics, site labour, and construction supervision. We don't subcontract your project and disappear — we stay accountable from brief to handover.</p>
                    <p>Our team operates from Kipawa, Dar es Salaam, with active projects across Tanzania including Kinyerezi and the coast of Zanzibar.</p>
                </div>
            </div>
            <ul class="ed-facts" data-reveal>
                <li><strong>End-to-end</strong><span>Brief, build, handover — one team</span></li>
                <li><strong>BRELA 626936</strong><span>Registered and legally operating</span></li>
                <li><strong>Multi-region</strong><span>Dar es Salaam, Zanzibar &amp; beyond</span></li>
            </ul>
        </div>
    </section>

    {{-- SERVICES --}}
    <section class="ed-section ed-concrete" aria-label="Services">
        <div class="ed-wrap">
            <div class="ed-head" data-reveal>
                <h2 class="ed-h2">Services that cover the whole project, not parts of it.</h2>
                <p class="ed-lead">Most construction problems don't come from bad builders — they come from gaps between builders, suppliers, and supervisors. Leivant closes those gaps. Every service is connected, coordinated, and managed by the same team handling your project from day one.</p>
            </div>
            <div class="ed-service-grid">
                @foreach ([
                    ['House construction', 'We plan, supervise, and deliver residential construction projects — new builds, floor extensions, structural renovations, and finishing work. Our site team manages labour scheduling, quality checks, and progress reporting so you know exactly where your build stands at every stage.', 'house-construction-tanzania', 'leivant-webp/20260608_162620.jpg.webp'],
                    ['BOQ & estimation', 'Before any project breaks ground, the numbers need to be right. Leivant prepares detailed Bills of Quantities, material schedules, and site-specific cost estimates — helping clients and developers understand the real cost of their build before committing funds or signing contracts.', 'boq-preparation-tanzania', 'leivant-webp/20260523_141830.jpg.webp'],
                    ['Equipment rental', 'We supply and coordinate construction machinery for active sites — excavators, concrete mixers, compactors, and supporting equipment. Every rental is assessed against site access conditions, project phase, and duration to avoid downtime and over-cost.', 'construction-equipment-rental', 'leivant-webp/20260608_162631.jpg.webp'],
                    ['Materials supply', 'Cement, steel reinforcement, building blocks, roofing sheets, tiles, and finishing materials — sourced, scheduled, and delivered to site. We coordinate materials against your build timeline so work isn\'t held up waiting for stock.', 'building-materials-supply', 'leivant-webp/20260523_142200.jpg.webp'],
                ] as [$title, $body, $slug, $image])
                    <article class="ed-service" data-reveal>
                        <div class="ed-service-media">
                            <img src="{{ asset($image) }}" alt="{{ $title }}" loading="lazy" decoding="async">
                        </div>
                        <div class="ed-service-body">
                            <h3>{{ $title }}</h3>
                            <p>{{ $body }}</p>
                            <a class="ed-link" href="{{ route('services.show', $slug) }}" aria-label="Learn more about {{ $title }}">Learn more</a>
                        </div>
                    </article>
                @endforeach
            </div>
            <p class="ed-more" data-reveal><a class="ed-link" href="{{ route('services.index') }}">Browse all services</a></p>
        </div>
    </section>

    {{-- WHY LEIVANT --}}
    <section class="ed-section ed-why" aria-label="Why Leivant">
        <div class="ed-wrap">
            <h2 class="ed-h2" data-reveal>Construction is complex. Working with Leivant shouldn't be.</h2>
            <div class="ed-why-grid">
                @foreach ([
                    ['One team accountable', 'You deal with one team — not a chain of subcontractors pointing fingers at each other. Leivant takes responsibility for the full project scope and keeps you informed at every stage.'],
                    ['Honest costs upfront', 'We don\'t win jobs with low quotes and inflate later. Our BOQ and estimation work is done carefully before any work starts — so there are no surprises mid-build when it\'s too late to change course.'],
                    ['Supervised on site', 'Your project doesn\'t just get handed to a crew and forgotten. Leivant\'s team supervises site work directly — checking quality, managing pace, and catching problems before they become costly.'],
                    ['Built for Tanzania', 'We work with local suppliers, understand local logistics, and know what builds actually require in Dar es Salaam, the coast, and upcountry — from road access challenges to seasonal material availability.'],
                ] as [$title, $body])
                    <article data-reveal>
                        <h3>{{ $title }}</h3>
                        <p>{{ $body }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PROJECTS + SITE DIARY --}}
    <section class="ed-section" aria-label="Projects">
        <div class="ed-wrap">
            <div class="ed-head" data-reveal>
                <h2 class="ed-h2">Real projects. Real sites. Documented from ground to finish.</h2>
                <div class="ed-lead">
                    <p>Below are four recent builds — a residential development in Kinyerezi, Dar es Salaam, a coastal construction project in Zanzibar, an active structural site in Dodoma, and a completed commercial complex. All managed end-to-end by the Leivant team, from early planning and material sourcing through supervised finishing and client handover.</p>
                    <p>Every project is site-photographed and documented. Clients deserve to see real work — not stock images and generic promises.</p>
                </div>
            </div>
            <div class="ed-project-grid">
                <a class="ed-project" href="{{ route('projects.index') }}" data-reveal>
                    <img src="{{ asset('leivant-webp/20260523_141609.jpg.webp') }}" alt="Kinyerezi residential construction by Leivant" loading="lazy" decoding="async">
                    <div class="ed-project-cap"><span>Kinyerezi, Dar es Salaam</span><strong>Residential new build, supervised site delivery</strong></div>
                </a>
                <a class="ed-project" href="{{ route('projects.index') }}" data-reveal>
                    <img src="{{ asset('leivant-webp/20260523_142319.jpg.webp') }}" alt="Zanzibar coastal construction by Leivant" loading="lazy" decoding="async">
                    <div class="ed-project-cap"><span>Zanzibar</span><strong>Coastal construction, materials coordination and finishing</strong></div>
                </a>
                <a class="ed-project" href="{{ route('projects.index') }}#dodoma" data-reveal>
                    <img src="{{ asset('leivant-webp/dodoma-progress-hero.webp') }}" alt="Dodoma structural site supervised by Leivant" loading="lazy" decoding="async">
                    <div class="ed-project-cap"><span>Dodoma · Active site</span><strong>Slab formwork and steel fixing under supervision</strong></div>
                </a>
                <a class="ed-project" href="{{ route('projects.index') }}#mapacha" data-reveal>
                    <img src="{{ asset('leivant-webp/mapacha-complex-hero.webp') }}" alt="Mapacha wa 3 Complex delivered by Leivant in Dar es Salaam" loading="lazy" decoding="async">
                    <div class="ed-project-cap"><span>Dar es Salaam · Delivered</span><strong>Commercial complex, handed over complete</strong></div>
                </a>
            </div>
            <p class="ed-more" data-reveal><a class="ed-link" href="{{ route('projects.index') }}">View full project gallery</a></p>

            <div class="ed-diary">
                <div class="ed-head" data-reveal>
                    <h2 class="ed-h2">Dodoma: slab week, photographed from the deck.</h2>
                    <p class="ed-lead">Formwork panels down, reinforcement going in, stair cores propped — our engineers walked every panel before the pour. This is what supervised delivery looks like.</p>
                </div>
                <div class="ed-diary-rail" tabindex="0" aria-label="Dodoma site photos, scroll sideways" data-reveal>
                    <figure>
                        <img src="{{ asset('leivant-webp/dodoma-progress-01.webp') }}" alt="Leivant supervisor checking slab reinforcement in Dodoma" loading="lazy" decoding="async">
                        <figcaption>Steel check</figcaption>
                    </figure>
                    <figure>
                        <img src="{{ asset('leivant-webp/dodoma-progress-02.webp') }}" alt="Staircase formwork and propping in Dodoma" loading="lazy" decoding="async">
                        <figcaption>Stair core</figcaption>
                    </figure>
                    <figure>
                        <img src="{{ asset('leivant-webp/dodoma-progress-03.webp') }}" alt="Rebar fixing close-up on the Dodoma slab" loading="lazy" decoding="async">
                        <figcaption>Rebar detail</figcaption>
                    </figure>
                    <figure>
                        <img src="{{ asset('leivant-webp/dodoma-progress-04.webp') }}" alt="Formwork deck ready for concrete in Dodoma" loading="lazy" decoding="async">
                        <figcaption>Deck ready for the pour</figcaption>
                    </figure>
                </div>
                <p class="ed-more" data-reveal><a class="ed-link" href="{{ route('projects.index') }}">See the full Dodoma build</a></p>
            </div>
        </div>
    </section>

    {{-- RESOURCES (live catalogue) --}}
    <section class="ed-section ed-concrete" aria-label="Resources">
        <div class="ed-wrap">
            <div class="ed-head" data-reveal>
                <h2 class="ed-h2">Materials and equipment for your active project.</h2>
                <div class="ed-lead">
                    <p>The catalogue covers building materials, site equipment, and project supplies needed from foundation work through finishing. Browse it to build a request list, or contact us if you already know what you need. We check availability, site logistics, and delivery timing before confirming any supply order.</p>
                    <p id="home-catalogue-count" aria-live="polite"></p>
                </div>
            </div>
            <div class="product-grid product-grid-compact" id="home-product-grid">
                <p class="loading">Loading catalogue…</p>
            </div>
            <p class="ed-more" data-reveal><a class="ed-link" href="{{ route('products.index') }}">Full catalogue</a></p>
        </div>
    </section>

    {{-- PROVIDERS (live) --}}
    <section class="ed-section" aria-label="Providers">
        <div class="ed-wrap">
            <div class="ed-head" data-reveal>
                <h2 class="ed-h2">Verified trade and site partners when you need extra capacity.</h2>
                <p class="ed-lead">Leivant coordinates vetted suppliers, trades, and equipment partners across Tanzania, listed live from our provider directory.</p>
            </div>
            <div class="provider-grid-compact" id="home-provider-grid">
                <p class="loading">Loading providers…</p>
            </div>
            <p class="ed-more" data-reveal><a class="ed-link" href="{{ route('discovery.index') }}">All providers</a></p>
        </div>
    </section>

    {{-- TRUST --}}
    <section class="ed-section ed-concrete" aria-label="Trust">
        <div class="ed-wrap ed-trust-grid">
            <h2 class="ed-h2" data-reveal>Why Tanzanian clients trust Leivant with their builds.</h2>
            <ul class="ed-trust">
                @foreach ([
                    ['BRELA registered business', 'No. 626936. Formally registered and legally operating in Tanzania.'],
                    ['Over a decade on site', 'Operating continuously across Dar es Salaam, Zanzibar, and beyond.'],
                    ['No middlemen', 'Leivant\'s own team handles your project. Not outsourced to unknown subcontractors.'],
                    ['Transparent cost planning', 'BOQ and estimation work is done before any commitment. You know the numbers before ground breaks.'],
                    ['Direct communication', 'Call, WhatsApp, or email. You reach the team handling your project — not a call centre.'],
                ] as [$title, $body])
                    <li data-reveal>
                        <span class="tick" aria-hidden="true">✓</span>
                        <div>
                            <strong>{{ $title }}</strong>
                            <p>{{ $body }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="ed-section ed-cta" aria-label="Contact">
        <div class="ed-wrap ed-cta-grid" data-reveal>
            <div>
                <h2>Ready to start? Send us your project brief.</h2>
                <p class="ed-lead">Planning a new residential build, extending an existing structure, or need materials and equipment coordinated for an active site? Start with a brief. Our team will review it and come back with a practical construction route specific to your project.</p>
                <p class="ed-cta-note">No obligation. No generic sales pitch. Just a clear, honest response from a team that knows Tanzanian sites.</p>
                <a href="{{ route('contact.index') }}" class="ed-btn ed-btn-dark">Send your project brief</a>
            </div>
            <div class="ed-contact-card">
                <p>Prefer to talk? Call us directly.</p>
                <a class="ed-contact-phone" href="tel:+255717970799">0717 970 799</a>
                <div class="ed-contact-row">
                    <a href="https://wa.me/255717970799" target="_blank" rel="noopener">Message on WhatsApp</a>
                    <a href="mailto:info@leivantconstruction.com">info@leivantconstruction.com</a>
                </div>
            </div>
        </div>
    </section>

    {{-- MOBILE CALL BAR --}}
    <nav class="ed-dock" aria-label="Quick contact">
        <a class="call" href="tel:+255717970799">Call us</a>
        <a class="wa" href="https://wa.me/255717970799" target="_blank" rel="noopener">WhatsApp</a>
    </nav>

</div>

@endsection