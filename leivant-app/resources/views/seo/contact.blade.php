@extends('layouts.seo')

@section('og_image', asset('images/site/international-client-support.jpg'))

@section('title', 'Contact Leivant Construction | Dar es Salaam Tanzania')
@section('meta_description', 'Contact Leivant Construction in Dar es Salaam for project briefs, building services, materials, equipment rental, BOQ, or site supervision.')
@section('canonical', route('contact.index'))

@section('content')
<div class="home-v2">

    {{-- HERO --}}
    <section class="proj-hero" aria-label="Contact Leivant">
        <img src="{{ asset('images/site/international-client-support.jpg') }}" alt="Leivant Construction Solutions supporting diaspora and international clients building in Tanzania" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
        <div class="ed-wrap proj-hero-inner">
            <p class="ed-label" style="color:var(--amber);" data-reveal>Contact Leivant</p>
            <h1 data-reveal>Tell Leivant what your <span class="amber">project needs.</span></h1>
            <p class="ed-lead" data-reveal>Send a project brief for construction services, equipment, materials, BOQ preparation, provider connection, or site coordination. We review every message personally.</p>
            <div class="ed-hero-cta" data-reveal>
                <a href="#contact-form" class="ed-btn ed-btn-solid">Send message</a>
                <a href="#website-feedback" class="ed-btn ed-btn-ghost">Website feedback</a>
                <a href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}" class="ed-btn ed-btn-ghost">Chat on WhatsApp</a>
            </div>
        </div>
    </section>

    {{-- FORM + INFO --}}
    <section id="contact-form" class="ed-section" style="scroll-margin-top:120px;" aria-label="Send a message">
        <div class="ed-wrap contact-layout">
            <div data-reveal>
                @include('seo.partials.inquiry-form', [
                    'formTitle' => 'Send a Project Message',
                    'defaultSubject' => 'Construction project inquiry',
                    'nextNote' => 'After submitting, Leivant reviews your scope, location, budget, and timeline before replying by your preferred contact method. We respond within one business day.',
                ])
            </div>
            <aside class="ct-info" data-reveal>
                <h3>Reach the Leivant Construction Desk</h3>
                <p class="ct-info-intro">We are a real construction company with a real office in Dar es Salaam. Every message is reviewed personally.</p>
                <div class="ct-info-list">
                    @foreach ([
                        ['Phone / WhatsApp', config('app.company.phone', '0717 970 799')],
                        ['Email', config('app.company.email', 'info@leivantconstruction.com')],
                        ['Office', 'Majumba site, Kipawa Airport, Dar es Salaam, Tanzania'],
                        ['Hours', 'Mon-Fri 8:00 AM-6:00 PM. Saturday by appointment.'],
                    ] as [$label, $value])
                        <div class="ct-info-row">
                            <span>▪</span>
                            <div>
                                <small>{{ $label }}</small>
                                <strong>{{ $value }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a class="ct-map-link" href="{{ config('app.company.map_url', 'https://www.google.com/maps?q=-6.8664952,39.1899987&z=17&hl=en') }}" target="_blank" rel="noopener">Open office location on Google Maps</a>
                <div class="ct-map">
                    <iframe title="Leivant Construction office map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=-6.8664952,39.1899987&z=16&output=embed"></iframe>
                    <a href="{{ config('app.company.map_url', 'https://www.google.com/maps?q=-6.8664952,39.1899987&z=17&hl=en') }}" target="_blank" rel="noopener">Open in Google Maps</a>
                </div>
                <div class="ct-process">
                    @foreach ([
                        ['1', 'Send the brief'],
                        ['2', 'Leivant reviews'],
                        ['3', 'You get next step'],
                    ] as [$number, $label])
                        <div><span>{{ $number }}</span><p>{{ $label }}</p></div>
                    @endforeach
                </div>
                <a class="ct-whatsapp" href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}">Chat on WhatsApp — 0717 970 799</a>
            </aside>
        </div>
    </section>

    @php
        $visitReasons = ['Find construction services', 'Request a project quote', 'Browse products or equipment', 'Find providers', 'Check company information', 'General website review'];
        $feedbackAreas = ['Navigation', 'Mobile view', 'Design', 'Service details', 'Products', 'Contact forms', 'Speed', 'Trust information'];
        $selectedFeedbackAreas = (array) old('feedback_areas', []);
    @endphp

    {{-- FEEDBACK --}}
    <section id="website-feedback" class="ed-section ct-feedback" style="scroll-margin-top:60px;" aria-label="Website feedback">
        <div class="ed-wrap ct-feedback-shell">
            <aside class="ct-command" data-reveal>
                <div>
                    <p class="ed-label">Website feedback</p>
                    <h2 class="ed-h2">Help Leivant improve the online experience.</h2>
                    <p class="ed-lead">Tell us what worked, what slowed you down, and what would make the website easier for clients, suppliers, and project owners.</p>
                    <div class="ct-route">
                        <div><strong>01</strong><span>Choose your purpose</span></div>
                        <div><strong>02</strong><span>Rate the experience</span></div>
                        <div><strong>03</strong><span>Send clear suggestions</span></div>
                    </div>
                </div>
                <div class="ct-admin-note">
                    <small>Delivered to admin</small>
                    <strong>admin@leivantconstruction.com</strong>
                </div>
            </aside>
            <form method="POST" action="{{ route('contact.feedback') }}" class="ct-form" data-reveal>
                @csrf
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="ct-hp" aria-hidden="true">
                <input type="hidden" name="page_url" value="{{ old('page_url', url()->current()) }}">
                <div class="ct-form-head">
                    <span>Visitor review</span>
                    <h3>Website Experience Report</h3>
                    <p>Keep it honest and practical. Short notes are fine, detailed notes are even better.</p>
                </div>
                <div class="form-grid">
                    <label>
                        <span>Full name *</span>
                        <input name="name" value="{{ old('name') }}" required autocomplete="name">
                        @error('name', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Why did you visit? *</span>
                        <select name="visit_reason" required>
                            @foreach ($visitReasons as $reason)
                                <option value="{{ $reason }}" @selected(old('visit_reason') === $reason)>{{ $reason }}</option>
                            @endforeach
                        </select>
                        @error('visit_reason', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email">
                        @error('email', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>Phone</span>
                        <input name="phone" value="{{ old('phone') }}" autocomplete="tel">
                        @error('phone', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                    </label>
                    <fieldset class="full" style="border:0;margin:0;padding:0;">
                        <legend style="margin-bottom:10px;font-size:12px;font-weight:700;color:#221d18;">Overall website rating *</legend>
                        <div class="ct-rating-grid">
                            @foreach ([5 => ['Excellent', 'Ready to use'], 4 => ['Good', 'Small fixes'], 3 => ['Average', 'Needs clarity'], 2 => ['Difficult', 'Needs work'], 1 => ['Poor', 'Hard to use']] as $value => [$label, $hint])
                                <label>
                                    <input type="radio" name="rating" value="{{ $value }}" @checked((string) old('rating', '5') === (string) $value) required>
                                    <span class="n">{{ $value }}</span>
                                    <strong>{{ $label }}</strong>
                                    <small>{{ $hint }}</small>
                                </label>
                            @endforeach
                        </div>
                        @error('rating', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                    </fieldset>
                    <fieldset class="full" style="border:0;margin:0;padding:0;">
                        <legend style="margin-bottom:10px;font-size:12px;font-weight:700;color:#221d18;">Which website areas should admin check?</legend>
                        <div class="ct-pills">
                            @foreach ($feedbackAreas as $area)
                                <label>
                                    <input type="checkbox" name="feedback_areas[]" value="{{ $area }}" @checked(in_array($area, $selectedFeedbackAreas, true))>
                                    <span>{{ $area }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('feedback_areas', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                    </fieldset>
                    <label class="full">
                        <span>Your website experience *</span>
                        <textarea name="experience" required placeholder="Example: I found the services quickly, but the products page needed clearer prices and filters.">{{ old('experience') }}</textarea>
                        @error('experience', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="full ct-consent">
                        <input type="checkbox" name="contact_permission" value="1" @checked(old('contact_permission'))>
                        <span>
                            <strong>Admin may contact me about this feedback</strong>
                            <small>Only if Leivant needs clarification before improving the website.</small>
                        </span>
                    </label>
                </div>
                <button class="ct-submit" type="submit">Send website feedback</button>
            </form>
        </div>
    </section>

</div>
@endsection

@push('head')
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
