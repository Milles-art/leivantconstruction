@php
  $hvNav = [
    ['label' => 'Home', 'url' => route('home'), 'match' => ['home']],
    ['label' => 'Services', 'url' => route('services.index'), 'match' => ['services.*']],
    ['label' => 'Projects', 'url' => route('projects.index'), 'match' => ['projects.*']],
    ['label' => 'Resources', 'url' => route('products.index'), 'match' => ['products.*', 'cart.*', 'checkout.*', 'orders.*']],
    ['label' => 'Providers', 'url' => route('discovery.index'), 'match' => ['discovery.*']],
    ['label' => 'About', 'url' => route('about.index'), 'match' => ['about.*']],
    ['label' => 'Contact', 'url' => route('contact.index'), 'match' => ['contact.*']],
  ];
  $hvPhone = config('app.company.phone', '0717 970 799');
@endphp

<header class="hv-header" id="hv-header">
  <div class="hv-header__bar">
    <a href="{{ route('home') }}" class="hv-brand" aria-label="Leivant home">
      <img src="{{ asset('images/leivant-logo-mark.svg') }}" alt="Leivant" class="hv-brand__mark" width="48" height="48">
      <span class="hv-brand__name">
        <strong>Leivant</strong>
        <small>Construction Solutions</small>
      </span>
    </a>

    <nav class="hv-nav" aria-label="Main navigation">
      @foreach ($hvNav as $item)
        <a href="{{ $item['url'] }}" @class(['is-active' => request()->routeIs(...$item['match'])])>{{ $item['label'] }}</a>
      @endforeach
    </nav>

    <div class="hv-header__actions">
      <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hvPhone) }}" class="hv-phone">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>
        {{ $hvPhone }}
      </a>
      <button type="button" class="hv-menu-btn" id="hv-menu-open" aria-label="Open menu" aria-expanded="false" aria-controls="hv-sheet">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
      </button>
    </div>
  </div>
  <div class="hv-progress" id="hv-progress" aria-hidden="true"></div>
</header>

<div class="hv-sheet" id="hv-sheet" aria-hidden="true">
  <div class="hv-sheet__backdrop" data-hv-close></div>
  <div class="hv-sheet__panel" role="dialog" aria-modal="true" aria-label="Site menu">
    <button type="button" class="hv-sheet__close" id="hv-menu-close" aria-label="Close menu">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
    <nav class="hv-sheet__nav" aria-label="Mobile navigation">
      @foreach ($hvNav as $item)
        <a href="{{ $item['url'] }}" @class(['is-active' => request()->routeIs(...$item['match'])])>{{ $item['label'] }}</a>
      @endforeach
    </nav>
    <div class="hv-sheet__foot">
      <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hvPhone) }}">{{ $hvPhone }}</a>
      <a href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}" target="_blank" rel="noopener">WhatsApp</a>
      <a href="mailto:{{ config('app.company.email', 'info@leivantconstruction.com') }}">{{ config('app.company.email', 'info@leivantconstruction.com') }}</a>
    </div>
  </div>
</div>

<script>
(function () {
  var header = document.getElementById('hv-header');
  var progress = document.getElementById('hv-progress');
  var sheet = document.getElementById('hv-sheet');
  var openBtn = document.getElementById('hv-menu-open');
  var closeBtn = document.getElementById('hv-menu-close');

  function onScroll() {
    var y = window.scrollY || window.pageYOffset;
    if (header) header.classList.toggle('is-scrolled', y > 8);
    if (progress) {
      var max = document.documentElement.scrollHeight - window.innerHeight;
      progress.style.transform = 'scaleX(' + (max > 0 ? Math.min(y / max, 1) : 0) + ')';
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  function setMenu(open) {
    if (!sheet) return;
    sheet.classList.toggle('is-open', open);
    sheet.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (openBtn) openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.style.overflow = open ? 'hidden' : '';
  }
  if (openBtn) openBtn.addEventListener('click', function () { setMenu(true); });
  if (closeBtn) closeBtn.addEventListener('click', function () { setMenu(false); });
  if (sheet) sheet.querySelectorAll('[data-hv-close]').forEach(function (el) {
    el.addEventListener('click', function () { setMenu(false); });
  });
  if (sheet) sheet.querySelectorAll('.hv-sheet__nav a').forEach(function (a) {
    a.addEventListener('click', function () { setMenu(false); });
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMenu(false); });
})();
</script>
