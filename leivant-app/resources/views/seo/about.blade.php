@extends('layouts.seo')

@section('og_image', asset('images/site/international-client-support.jpg'))

@section('title', 'Contact Leivant Construction | Dar es Salaam Tanzania')
@section('meta_description', 'Contact Leivant Construction in Dar es Salaam for project briefs, building services, materials, equipment rental, BOQ, or site supervision.')
@section('canonical', route('contact.index'))

@section('content')
  @php
      $visitReasons = ['Find construction services', 'Request a project quote', 'Browse products or equipment', 'Find providers', 'Check company information', 'General website review'];
      $feedbackAreas = ['Navigation', 'Mobile view', 'Design', 'Service details', 'Products', 'Contact forms', 'Speed', 'Trust information'];
      $selectedFeedbackAreas = (array) old('feedback_areas', []);
      $feedbackOpen = ($errors->hasBag('websiteFeedback') && $errors->getBag('websiteFeedback')->any()) || old('experience');
  @endphp

  <div class="hv-page">

    {{-- HERO --}}
    <section class="hv-hero" aria-label="Project enquiries">
      <div class="hv-hero__media">
        <img src="{{ asset('images/site/international-client-support.jpg') }}" alt="Leivant Construction supporting clients in Tanzania" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
      </div>
      <div class="hv-hero__inner">
        <p class="hv-eyebrow">Contact Leivant</p>
        <h1 class="hv-hero__title">Tell us about your <span class="hv-amber">project.</span></h1>
        <p class="hv-hero__lead">Send a brief and Leivant replies within one business day.</p>
        <div class="hv-hero__actions">
          <a href="#contact-form" class="hv-btn">Send a Project Message</a>
          <a href="#website-feedback" class="hv-btn hv-btn--ghost">Website feedback</a>
        </div>
      </div>
    </section>

    {{-- CONTACT FORM + DETAILS --}}
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
          <p class="hv-eyebrow">Project desk</p>
          <h2 class="hv-title" style="font-size:clamp(32px,3.4vw,52px);">Leivant Construction Desk</h2>
          <ul class="hv-list">
            <li>
              <div>
                <strong class="hv-mono hv-amber" style="font-size:11px;">Phone</strong>
                <p><a href="tel:{{ config('app.company.phone_tel', '+255717970799') }}">{{ config('app.company.phone', '0717 970 799') }}</a></p>
              </div>
            </li>
            <li>
              <div>
                <strong class="hv-mono hv-amber" style="font-size:11px;">WhatsApp</strong>
                <p><a href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}" target="_blank" rel="noopener">Chat with us</a></p>
              </div>
            </li>
            <li>
              <div>
                <strong class="hv-mono hv-amber" style="font-size:11px;">Email</strong>
                <p><a href="mailto:{{ config('app.company.email', 'info@leivantconstruction.com') }}">{{ config('app.company.email', 'info@leivantconstruction.com') }}</a></p>
              </div>
            </li>
            <li>
              <div>
                <strong class="hv-mono hv-amber" style="font-size:11px;">Office</strong>
                <p>Majumba site, Kipawa Airport, Dar es Salaam, Tanzania</p>
              </div>
            </li>
            <li>
              <div>
                <strong class="hv-mono hv-amber" style="font-size:11px;">Hours</strong>
                <p>Mon–Fri 8:00 AM–6:00 PM. Saturday by appointment.</p>
              </div>
            </li>
          </ul>
          <div style="margin-top:24px;">
            <iframe title="Leivant Construction office map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=-6.8664952,39.1899987&z=16&output=embed" style="display:block;width:100%;height:240px;border:0;border-radius:3px;filter:grayscale(.4);"></iframe>
            <a href="{{ config('app.company.map_url', 'https://www.google.com/maps?q=-6.8664952,39.1899987&z=17&hl=en') }}" target="_blank" rel="noopener" class="hv-pill" style="margin-top:12px;">Open in Google Maps</a>
          </div>
        </aside>
      </div>
    </section>

    {{-- WEBSITE FEEDBACK --}}
    <section class="hv-section hv-section--surface" aria-label="Website feedback">
      <div class="hv-wrap">
        <details id="website-feedback" style="scroll-margin-top:80px;" @if ($feedbackOpen) open @endif>
          <summary style="display:flex;align-items:center;justify-content:space-between;gap:20px;padding:6px 0;list-style:none;cursor:pointer;">
            <span class="hv-title" style="font-size:clamp(30px,3.4vw,46px);margin:0;">Website feedback</span>
            <small class="hv-muted">Tell us how we can improve the site.</small>
          </summary>

          <form method="POST" action="{{ route('contact.feedback') }}" class="hv-form" style="margin-top:32px;">
            @csrf
            <input type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
            <input type="hidden" name="page_url" value="{{ old('page_url', url()->current()) }}">
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
              <legend style="margin-bottom:10px;padding:0;font-family:var(--hv-mono);font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:var(--hv-amber-lt);">Overall rating *</legend>
              <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px;">
                @foreach ([5 => 'Excellent', 4 => 'Good', 3 => 'Average', 2 => 'Difficult', 1 => 'Poor'] as $value => $label)
                  <label style="position:relative;display:grid;justify-items:center;gap:2px;padding:12px 6px;background:var(--hv-card);border:1px solid var(--hv-line);border-radius:3px;cursor:pointer;text-align:center;">
                    <input type="radio" name="rating" value="{{ $value }}" @checked((string) old('rating', '5') === (string) $value) required style="position:absolute;opacity:0;width:auto;min-height:0;">
                    <span class="hv-mono hv-amber" style="font-size:24px;line-height:1;">{{ $value }}</span>
                    <strong style="font-size:13px;">{{ $label }}</strong>
                  </label>
                @endforeach
              </div>
              @error('rating', 'websiteFeedback') <small>{{ $message }}</small> @enderror
            </fieldset>

            <fieldset style="min-width:0;border:0;margin:0;padding:0;">
              <legend style="margin-bottom:10px;padding:0;font-family:var(--hv-mono);font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:var(--hv-amber-lt);">Which areas should we check?</legend>
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
              <span>Your experience *</span>
              <textarea name="experience" required placeholder="What worked, and what slowed you down?">{{ old('experience') }}</textarea>
              @error('experience', 'websiteFeedback') <small>{{ $message }}</small> @enderror
            </label>

            <label style="display:flex;align-items:flex-start;gap:12px;">
              <input type="checkbox" name="contact_permission" value="1" @checked(old('contact_permission')) style="width:20px;height:20px;min-height:0;margin-top:2px;accent-color:var(--hv-amber);">
              <span>
                <strong style="display:block;color:var(--hv-text);font-size:15px;">You may contact me about this feedback</strong>
                <small class="hv-muted">Only if Leivant needs clarification.</small>
              </span>
            </label>

            <button class="hv-btn" type="submit" style="justify-self:start;">Send feedback</button>
          </form>
        </details>
      </div>
    </section>

    {{-- CLOSING CTA --}}
    <section class="hv-cta" aria-label="Contact">
      <h2 class="hv-cta__title">Tell us about your project.</h2>
      <p class="hv-cta__text">Send a brief and Leivant replies within one business day.</p>
      <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:30px;" class="hv-mono">
        <a href="tel:{{ config('app.company.phone_tel', '+255717970799') }}" class="hv-pill">{{ config('app.company.phone', '0717 970 799') }}</a>
        <a href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}" target="_blank" rel="noopener" class="hv-pill">WhatsApp</a>
        <a href="mailto:{{ config('app.company.email', 'info@leivantconstruction.com') }}" class="hv-pill">{{ config('app.company.email', 'info@leivantconstruction.com') }}</a>
      </div>
      <a href="#contact-form" class="hv-btn">Send a Project Message</a>
    </section>

  </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var details = document.getElementById('website-feedback');
            if (!details) return;
            function openFromHash() {
                if (location.hash === '#website-feedback') {
                    details.open = true;
                    details.scrollIntoView();
                }
            }
            openFromHash();
            window.addEventListener('hashchange', openFromHash);
        })();
    </script>
@endpush

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
