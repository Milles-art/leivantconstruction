@extends('layouts.seo')

@section('title', 'Leivant Construction Projects in Tanzania')
@section('meta_description', 'See Leivant Construction Solutions project work in Dar es Salaam, Zanzibar, and Dodoma — real site photography, scope, and delivery approach.')
@section('canonical', route('projects.index'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
@endpush

@section('content')
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

  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Projects introduction">
      <div class="hv-hero__media">
        <img src="{{ asset($projects[0]['hero']) }}" alt="" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow"><x-nav-icon name="projects" /> Our projects</p>
        <h1 class="hv-hero__title">Real Leivant construction work <span class="hv-amber">in Tanzania</span></h1>
        <p class="hv-hero__lead">
          Photography and project notes from active and completed sites — not stock images or generic categories. These are four projects Leivant has planned, built, and supervised on the ground.
        </p>
        <div class="hv-stats hv-mt">
          @foreach ([[count($projects), 'Featured projects'], [$regionCount, 'Regions'], [$activeCount, 'Active sites']] as [$n, $l])
            <div class="hv-stat">
              <div class="hv-stat__value">{{ $n }}</div>
              <div class="hv-stat__label">{{ $l }}</div>
            </div>
          @endforeach
        </div>
        <div class="hv-hero__actions">
          <a href="{{ route('contact.index') }}" class="hv-btn">Discuss a similar project</a>
          <a href="#{{ $projects[0]['slug'] }}" class="hv-btn hv-btn--ghost">View projects</a>
        </div>
      </div>
    </section>

    {{-- JUMP NAV --}}
    <nav aria-label="Projects" class="hv-section hv-section--tight" style="padding-top:24px;padding-bottom:24px;">
      <div class="hv-wrap" style="display:flex;flex-wrap:wrap;gap:10px;">
        @foreach ($projects as $project)
          <a href="#{{ $project['slug'] }}" class="hv-pill">{{ $project['region'] }}</a>
        @endforeach
      </div>
    </nav>

    {{-- PROJECTS --}}
    @foreach ($projects as $index => $project)
      <section id="{{ $project['slug'] }}" class="hv-section{{ $index % 2 === 1 ? ' hv-section--surface' : '' }}" style="scroll-margin-top:80px;" aria-label="{{ $project['name'] }}">
        <div class="hv-wrap hv-split">
          <div @if ($index % 2 === 1) style="order:2;" @endif>
            <span class="hv-card__num" style="position:static;display:block;font-size:clamp(56px,6vw,88px);">{{ sprintf('%02d', $index + 1) }}</span>
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin:8px 0 20px;">
              <span class="hv-pill" style="border-color:var(--hv-amber);color:var(--hv-amber-lt);">{{ $project['type'] }}</span>
              <span class="hv-pill">
                @if ($project['active'])
                  <span aria-hidden="true" style="display:inline-block;width:8px;height:8px;margin-right:6px;border-radius:50%;background:#2fa763;box-shadow:0 0 0 3px rgba(47,167,99,.25);"></span>
                @endif
                {{ $project['status'] }}
              </span>
            </div>
            <h2 class="hv-title">{{ $project['name'] }}</h2>
            <p class="hv-muted" style="display:flex;align-items:center;gap:8px;margin:0 0 20px;font-size:17px;"><x-nav-icon name="map" /> {{ $project['location'] }}</p>
            <p class="hv-lead">{{ $project['summary'] }}</p>
            <ol class="hv-list">
              @foreach ($project['details'] as $i => $detail)
                <li>
                  <span class="hv-mono hv-amber" style="font-size:12px;">{{ sprintf('%02d', $i + 1) }}</span>
                  <p>{{ $detail }}</p>
                </li>
              @endforeach
            </ol>
            <a href="{{ route('contact.index') }}" class="hv-btn hv-mt">Discuss a similar project</a>
          </div>

          <div class="hv-split__media" @if ($index % 2 === 1) style="order:1;" @endif>
            <button type="button" class="proj-hero-shot" data-lightbox
                    data-src="{{ asset($project['hero']) }}"
                    data-alt="{{ $project['name'] }} — Leivant construction site in {{ $project['region'] }}"
                    aria-label="Enlarge photo: {{ $project['name'] }}"
                    style="position:relative;display:block;width:100%;aspect-ratio:16 / 11;margin:0;padding:0;overflow:hidden;background:var(--hv-card);border:0;border-radius:3px;cursor:zoom-in;">
              <img src="{{ asset($project['hero']) }}"
                   alt="{{ $project['name'] }} — Leivant construction site in {{ $project['region'] }}"
                   loading="lazy" decoding="async"
                   style="display:block;width:100%;height:100%;object-fit:cover;">
              <span class="hv-pill" style="position:absolute;top:14px;left:14px;background:var(--hv-bg);">{{ $project['region'] }}</span>
              <span style="position:absolute;right:14px;bottom:14px;padding:9px 14px;border-radius:3px;background:var(--hv-text);color:var(--hv-bg);font-family:var(--hv-mono);font-size:11px;letter-spacing:.12em;text-transform:uppercase;">Enlarge</span>
            </button>
            <div class="hv-grid hv-grid--4" style="margin-top:10px;">
              @foreach ($project['photos'] as $photo)
                <button type="button" data-lightbox
                        data-src="{{ asset($photo) }}"
                        data-alt="{{ $project['name'] }} site progress — Leivant Construction"
                        aria-label="Enlarge site photo"
                        style="position:relative;display:block;width:100%;aspect-ratio:1 / 1;margin:0;padding:0;overflow:hidden;background:var(--hv-card);border:0;border-radius:3px;cursor:zoom-in;">
                  <img src="{{ asset($photo) }}"
                       alt="{{ $project['name'] }} site progress — Leivant Construction"
                       loading="lazy" decoding="async"
                       style="display:block;width:100%;height:100%;object-fit:cover;">
                </button>
              @endforeach
            </div>
          </div>
        </div>
      </section>
    @endforeach

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Start a project">
      <h2 class="hv-cta__title">Planning a project in Dar es Salaam, Zanzibar or Dodoma?</h2>
      <p class="hv-cta__text">Send your brief with location, drawings, and timeline. Leivant reviews the site reality and responds with a practical next step.</p>
      <div class="hv-hero__actions" style="justify-content:center;">
        <a href="{{ route('contact.index') }}" class="hv-btn">Send project brief</a>
        <a href="tel:+255717970799" class="hv-btn hv-btn--ghost">Call 0717 970 799</a>
      </div>
    </section>

  </div>

  {{-- LIGHTBOX (native dialog, no libraries) --}}
  <dialog id="lightbox" class="proj-lightbox" aria-label="Photo viewer" style="width:100%;max-width:none;height:100%;max-height:none;margin:0;padding:0;border:0;background:transparent;">
    <div class="proj-lightbox-wrap" style="position:relative;display:grid;place-items:center;width:100%;height:100%;padding:56px 16px 24px;">
      <img id="lightbox-img" src="" alt="" style="display:block;max-width:min(96vw,1600px);max-height:calc(100vh - 96px);width:auto;height:auto;object-fit:contain;border-radius:3px;">
      <button type="button" id="lightbox-close" class="proj-lightbox-close" aria-label="Close" style="position:absolute;top:12px;right:12px;display:grid;place-items:center;width:48px;height:48px;border:1px solid rgba(255,255,255,.4);border-radius:50%;background:rgba(255,255,255,.08);color:#fff;font-size:18px;cursor:pointer;">✕</button>
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
