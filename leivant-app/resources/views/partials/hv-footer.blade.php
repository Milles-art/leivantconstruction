<footer class="hv-footer">
  <div class="hv-footer__top">
    <div>
      <p class="hv-footer__intro">Construction planning, building, and site delivery across Tanzania. One team, from brief to handover.</p>
      <p class="hv-footer__brela">BRELA 626936</p>
  <img src="{{ asset('images/leivant-logo-mark.svg') }}" alt="Leivant" class="hv-footer__logo" width="56" height="56">
    </div>
    <div>
      <h3>Explore</h3>
      <ul>
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('services.index') }}">Services</a></li>
        <li><a href="{{ route('projects.index') }}">Projects</a></li>
        <li><a href="{{ route('products.index') }}">Resources</a></li>
        <li><a href="{{ route('discovery.index') }}">Providers</a></li>
        <li><a href="{{ route('about.index') }}">About</a></li>
        <li><a href="{{ route('contact.index') }}">Contact</a></li>
      </ul>
    </div>
    <div>
      <h3>Contact</h3>
      <ul>
        <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', config('app.company.phone', '0717 970 799')) }}">0717 970 799</a></li>
        <li><a href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}" target="_blank" rel="noopener">WhatsApp</a></li>
        <li><a href="mailto:{{ config('app.company.email', 'info@leivantconstruction.com') }}">{{ config('app.company.email', 'info@leivantconstruction.com') }}</a></li>
        <li>Kipawa, Dar es Salaam, Tanzania</li>
      </ul>
    </div>
  </div>
  <p class="hv-footer__word" aria-hidden="true">Leivant</p>
  <div class="hv-footer__legal">
    <p>&copy; {{ date('Y') }} Leivant Construction Solutions. All rights reserved.</p>
  </div>
</footer>
