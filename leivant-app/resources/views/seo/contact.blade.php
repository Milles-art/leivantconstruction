@extends('layouts.seo')

@section('og_image', asset('images/site/international-client-support.jpg'))

@section('title', 'Contact Leivant Construction | Dar es Salaam Tanzania')
@section('meta_description', 'Contact Leivant Construction in Dar es Salaam for project briefs, building services, materials, equipment rental, BOQ, or site supervision.')
@section('canonical', route('contact.index'))

@section('content')
  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Contact Leivant">
      <div class="hv-hero__media">
        <img src="{{ asset('images/site/international-client-support.jpg') }}" alt="Leivant Construction Solutions supporting diaspora and international clients building in Tanzania" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">Contact Leivant</p>
        <h1 class="hv-hero__title">Tell Leivant what your <span class="hv-amber">project needs.</span></h1>
        <p class="hv-hero__lead">Send a project brief for construction services, equipment, materials, BOQ preparation, provider connection, or site coordination. We review every message personally.</p>
        <div class="hv-hero__actions">
          <a href="#contact-form" class="hv-btn">Send message</a>
          <a href="#website-feedback" class="hv-btn hv-btn--ghost">Website feedback</a>
          <a href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}" class="hv-btn hv-btn--ghost">Chat on WhatsApp</a>
        </div>
      </div>
    </section>

    {{-- FORM + INFO --}}
    <section id="contact-form" class="hv-section" style="scroll-margin-top:120px;" aria-label="Send a message">
      <div class="hv-wrap hv-split">
        <div class="hv-card" style="min-height:0;">
          @include('seo.partials.inquiry-form', [
              'formTitle' => 'Send a Project Message',
              'defaultSubject' => 'Construction project inquiry',
              'nextNote' => 'After submitting, Leivant reviews your scope, location, budget, and timeline before replying by your preferred contact method. We respond within one business day.',
          ])
        </div>

        <aside>
          <p class="hv-eyebrow">Reach the desk</p>
          <h2 class="hv-title" style="font-size:clamp(32px,3.4vw,52px);">Reach the Leivant Construction Desk</h2>
          <p class="hv-lead">We are a real construction company with a real office in Dar es Salaam. Every message is reviewed personally.</p>
          <ul class="hv-list">
            @foreach ([
                ['Phone / WhatsApp', config('app.company.phone', '0717 970 799')],
                ['Email', config('app.company.email', 'info@leivantconstruction.com')],
                ['Office', 'Majumba site, Kipawa Airport, Dar es Salaam, Tanzania'],
                ['Hours', 'Mon-Fri 8:00 AM-6:00 PM. Saturday by appointment.'],
            ] as [$label, $value])
              <li>
                <div>
                  <strong class="hv-mono hv-amber" style="font-size:11px;">{{ $label }}</strong>
                  <p>{{ $value }}</p>
                </div>
              </li>
            @endforeach
          </ul>
          <a class="hv-pill" style="display:inline-block;margin-top:24px;" href="{{ config('app.company.map_url', 'https://www.google.com/maps?q=-6.8664952,39.1899987&z=17&hl=en') }}" target="_blank" rel="noopener">Open office location on Google Maps</a>
          <div style="margin-top:20px;">
            <iframe title="Leivant Construction office map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=-6.8664952,39.1899987&z=16&output=embed" style="display:block;width:100%;height:240px;border:0;border-radius:3px;filter:grayscale(.4);"></iframe>
            <a href="{{ config('app.company.map_url', 'https://www.google.com/maps?q=-6.8664952,39.1899987&z=17&hl=en') }}" target="_blank" rel="noopener" class="hv-pill" style="margin-top:12px;">Open in Google Maps</a>
          </div>
          <div class="hv-grid hv-grid--3 hv-mt">
            @foreach ([
                ['1', 'Send the brief'],
                ['2', 'Leivant reviews'],
                ['3', 'You get next step'],
            ] as [$number, $label])
              <article class="hv-card" style="min-height:0;padding:20px;">
                <span class="hv-card__num" style="font-size:44px;">{{ $number }}</span>
                <h3 class="hv-card__title" style="font-size:20px;margin:0;">{{ $label }}</h3>
              </article>
            @endforeach
          </div>
          <a class="hv-btn hv-btn--ghost hv-mt" href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}">Chat on WhatsApp — 0717 970 799</a>
        </aside>
      </div>
    </section>

    @php
        $visitReasons = ['Find construction services', 'Request a project quote', 'Browse products or equipment', 'Find providers', 'Check company information', 'General website review'];
        $feedbackAreas = ['Navigation', 'Mobile view', 'Design', 'Service details', 'Products', 'Contact forms', 'Speed', 'Trust information'];
        $selectedFeedbackAreas = (array) old('feedback_areas', []);
    @endphp

    {{-- WEBSITE FEEDBACK --}}
    <section id="website-feedback" class="hv-section hv-section--surface" style="scroll-margin-top:60px;" aria-label="Website feedback">
      <div class="hv-wrap hv-split">
        <aside>
          <p class="hv-eyebrow">Website feedback</p>
          <h2 class="hv-title">Help Leivant improve the online experience.</h2>
          <p class="hv-lead">Tell us what worked, what slowed you down, and what would make the website easier for clients, suppliers, and project owners.</p>
          <div class="hv-grid hv-grid--3 hv-mt">
            @foreach ([
                ['01', 'Choose your purpose'],
                ['02', 'Rate the experience'],
                ['03', 'Send clear suggestions'],
            ] as [$number, $label])
              <article class="hv-card" style="min-height:0;padding:20px;">
                <span class="hv-card__num" style="font-size:44px;">{{ $number }}</span>
                <h3 class="hv-card__title" style="font-size:20px;margin:0;">{{ $label }}</h3>
              </article>
            @endforeach
          </div>
          <div class="hv-mt">
            <small class="hv-mono hv-muted" style="font-size:10px;">Delivered to admin</small>
            <strong class="hv-mono hv-amber" style="display:block;margin-top:4px;font-size:12px;">admin@leivantconstruction.com</strong>
          </div>
        </aside>

        <form method="POST" action="{{ route('contact.feedback') }}" class="hv-form">
          @csrf
          <input type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
          <input type="hidden" name="page_url" value="{{ old('page_url', url()->current()) }}">
          <div class="hv-head" style="margin-bottom:8px;">
            <p class="hv-eyebrow">Visitor review</p>
            <h3 class="hv-title" style="font-size:clamp(28px,3vw,40px);margin:10px 0;">Website Experience Report</h3>
            <p class="hv-muted">Keep it honest and practical. Short notes are fine, detailed notes are even better.</p>
          </div>
          <div class="hv-form__row">
            <label class="hv-field">
              <span>Full name *</span>
              <input name="name" value="{{ old('name') }}" required autocomplete="name">
              @error('name', 'websiteFeedback') <small>{{ $message }}</small> @enderror
            </label>
            <label class="hv-field">
              <span>Why did you visit? *</span>
              <select name="visit_reason" required>
                  @foreach ($visitReasons as $reason)
                      <option value="{{ $reason }}" @selected(old('visit_reason') === $reason)>{{ $reason }}</option>
                  @endforeach
              </select>
              @error('visit_reason', 'websiteFeedback') <small>{{ $message }}</small> @enderror
            </label>
            <label class="hv-field">
              <span>Email</span>
              <input type="email" name="email" value="{{ old('email') }}" autocomplete="email">
              @error('email', 'websiteFeedback') <small>{{ $message }}</small> @enderror
            </label>
            <label class="hv-field">
              <span>Phone</span>
              <input name="phone" value="{{ old('phone') }}" autocomplete="tel">
              @error('phone', 'websiteFeedback') <small>{{ $message }}</small> @enderror
            </label>
          </div>

          <fieldset style="min-width:0;border:0;margin:0;padding:0;">
            <legend style="margin-bottom:10px;padding:0;font-family:var(--hv-mono);font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:var(--hv-amber-lt);">Overall website rating *</legend>
            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px;">
              @foreach ([5 => ['Excellent', 'Ready to use'], 4 => ['Good', 'Small fixes'], 3 => ['Average', 'Needs clarity'], 2 => ['Difficult', 'Needs work'], 1 => ['Poor', 'Hard to use']] as $value => [$label, $hint])
                <label style="position:relative;display:grid;justify-items:center;gap:2px;padding:12px 6px;background:var(--hv-card);border:1px solid var(--hv-line);border-radius:3px;cursor:pointer;text-align:center;">
                  <input type="radio" name="rating" value="{{ $value }}" @checked((string) old('rating', '5') === (string) $value) required style="position:absolute;opacity:0;width:auto;min-height:0;">
                  <span class="hv-mono hv-amber" style="font-size:24px;line-height:1;">{{ $value }}</span>
                  <strong style="font-size:13px;">{{ $label }}</strong>
                  <small class="hv-muted">{{ $hint }}</small>
                </label>
              @endforeach
            </div>
            @error('rating', 'websiteFeedback') <small>{{ $message }}</small> @enderror
          </fieldset>

          <fieldset style="min-width:0;border:0;margin:0;padding:0;">
            <legend style="margin-bottom:10px;padding:0;font-family:var(--hv-mono);font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:var(--hv-amber-lt);">Which website areas should admin check?</legend>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
              @foreach ($feedbackAreas as $area)
                <label style="position:relative;display:inline-flex;">
                  <input type="checkbox" name="feedback_areas[]" value="{{ $area }}" @checked(in_array($area, $selectedFeedbackAreas, true)) style="position:absolute;opacity:0;width:auto;min-height:0;">
                  <span class="hv-pill">{{ $area }}</span>
                </label>
              @endforeach
            </div>
            @error('feedback_areas', 'websiteFeedback') <small>{{ $message }}</small> @enderror
          </fieldset>

          <label class="hv-field">
            <span>Your website experience *</span>
            <textarea name="experience" required placeholder="Example: I found the services quickly, but the products page needed clearer prices and filters.">{{ old('experience') }}</textarea>
            @error('experience', 'websiteFeedback') <small>{{ $message }}</small> @enderror
          </label>

          <label style="display:flex;align-items:flex-start;gap:12px;">
            <input type="checkbox" name="contact_permission" value="1" @checked(old('contact_permission')) style="width:20px;height:20px;min-height:0;margin-top:2px;accent-color:var(--hv-amber);">
            <span>
              <strong style="display:block;color:var(--hv-text);font-size:15px;">Admin may contact me about this feedback</strong>
              <small class="hv-muted">Only if Leivant needs clarification before improving the website.</small>
            </span>
          </label>

          <button class="hv-btn" type="submit" style="justify-self:start;">Send website feedback</button>
        </form>
      </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
      <h2 class="hv-cta__title">Tell Leivant what your project needs.</h2>
      <p class="hv-cta__text">Send a project brief for construction services, equipment, materials, BOQ preparation, provider connection, or site coordination. We review every message personally.</p>
      <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
        <a href="tel:+255717970799" class="hv-pill">0717 970 799</a>
        <a href="https://wa.me/255717970799" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
        <a href="mailto:info@leivantconstruction.com" class="hv-pill">info@leivantconstruction.com</a>
      </div>
      <a href="#contact-form" class="hv-btn">Send message</a>
    </section>

  </div>
@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('css/hv-system.css') }}?v=20261116">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => 'Contact Leivant Construction Solutions',
            'url' => route('contact.index'),
            'mainEntity' => [
                '@type' => 'Organization',
                'name' => 'Leivant Construction Solutions Company',
                'telephone' => config('app.company.phone_tel', '+255717970799'),
                'email' => config('app.company.email', 'info@leivantconstruction.com'),
                'address' => 'Majumba site, Kipawa Airport, Dar es Salaam, Tanzania',
                'hasMap' => config('app.company.map_url', 'https://www.google.com/maps?q=-6.8664952,39.1899987&z=17&hl=en'),
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => (float) config('app.company.latitude', -6.8664952),
                    'longitude' => (float) config('app.company.longitude', 39.1899987),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush
