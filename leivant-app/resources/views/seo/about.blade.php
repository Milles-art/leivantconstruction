@extends('layouts.seo')

@section('og_image', asset('images/site/international-client-support.jpg'))

@section('title', 'Contact Leivant Construction | Dar es Salaam Tanzania')
@section('meta_description', 'Contact Leivant Construction in Dar es Salaam for project briefs, building services, materials, equipment rental, BOQ, or site supervision.')
@section('canonical', route('contact.index'))

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap">

<style>
/* ---------- tokens ---------- */
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
    --error: #b3261e;

    --display: "Barlow Condensed", "Arial Narrow", system-ui, sans-serif;
    --text: "Barlow", system-ui, -apple-system, "Segoe UI", sans-serif;

    --pad: clamp(18px, 3.2vw, 56px);
    --radius: 4px;

    width: 100%;
    background: var(--paper);
    color: var(--ink);
    font-family: var(--text);
    overflow-x: clip;
}
.home-v2 a { color: inherit; }
.home-v2 :focus-visible { outline: 3px solid var(--amber); outline-offset: 3px; }

.home-v2 .ed-wrap { width: 100%; max-width: none; margin-inline: 0; padding-inline: var(--pad); }
.home-v2 .ed-section { padding: clamp(40px, 5vw, 72px) 0 clamp(64px, 8vw, 112px); }

/* ---------- header ---------- */
.home-v2 .cm-head { padding: clamp(48px, 7vw, 104px) 0 0; }
.home-v2 .cm-head h1 { max-width: 18ch; margin: 0; font: 700 clamp(48px, 7vw, 104px)/.92 var(--display); letter-spacing: -.012em; }
.home-v2 .cm-head p { max-width: 56ch; margin: 20px 0 0; color: var(--muted); font-size: 19px; line-height: 1.6; }

/* ---------- layout ---------- */
.home-v2 .contact-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.3fr) minmax(280px, .7fr);
    gap: clamp(40px, 7vw, 120px);
    align-items: start;
}
.home-v2 .contact-layout > * { min-width: 0; }

/* ---------- shared form fields (inquiry form + feedback form) ---------- */
.home-v2 .contact-layout h2,
.home-v2 .contact-layout h3 { margin: 0 0 20px; font: 700 clamp(30px, 3vw, 40px)/1 var(--display); }
.home-v2 .contact-layout p { color: var(--muted); line-height: 1.6; }

.home-v2 .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
.home-v2 .form-grid .full { grid-column: 1 / -1; }
.home-v2 .form-grid label { display: grid; gap: 7px; min-width: 0; align-content: start; }
.home-v2 .form-grid label > span { font: 600 14px/1.2 var(--text); }
.home-v2 .form-grid small { color: var(--error); font-size: 13px; }

.home-v2 .contact-layout input:not([type="checkbox"]):not([type="radio"]),
.home-v2 .ct-form input:not([type="checkbox"]):not([type="radio"]),
.home-v2 .contact-layout select,
.home-v2 .ct-form select,
.home-v2 .contact-layout textarea,
.home-v2 .ct-form textarea {
    width: 100%;
    min-height: 52px;
    padding: 0 14px;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    color: var(--ink);
    font: 500 16px/1.4 var(--text);
}
.home-v2 .contact-layout textarea,
.home-v2 .ct-form textarea { min-height: 150px; padding: 14px; resize: vertical; }
.home-v2 .contact-layout input:focus,
.home-v2 .contact-layout select:focus,
.home-v2 .contact-layout textarea:focus,
.home-v2 .ct-form input:focus,
.home-v2 .ct-form select:focus,
.home-v2 .ct-form textarea:focus { border-color: var(--ink); outline: 3px solid rgba(199,146,46,.35); outline-offset: 0; }

.home-v2 .contact-layout button[type="submit"],
.home-v2 .ct-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 54px;
    margin-top: 22px;
    padding: 0 30px;
    background: var(--ink);
    border: 2px solid var(--ink);
    border-radius: var(--radius);
    color: #fff;
    font: 600 16px/1 var(--text);
    cursor: pointer;
    transition: background .2s ease, color .2s ease, border-color .2s ease;
}
.home-v2 .contact-layout button[type="submit"]:hover,
.home-v2 .ct-submit:hover { background: var(--amber); border-color: var(--amber); color: #15130c; }

/* ---------- contact details ---------- */
.home-v2 .ct-info { padding-top: 6px; }
.home-v2 .ct-info-list { margin: 0; border-top: 2px solid var(--ink); }
.home-v2 .ct-info-row { padding: 18px 0; border-bottom: 1px solid var(--line); }
.home-v2 .ct-info-row small { display: block; margin-bottom: 4px; color: var(--muted); font-size: 14px; }
.home-v2 .ct-info-row strong,
.home-v2 .ct-info-row a { display: block; font: 600 19px/1.4 var(--text); text-decoration: none; overflow-wrap: anywhere; }
.home-v2 .ct-info-row a:hover { color: var(--amber-deep); }

.home-v2 .ct-map { margin-top: 24px; }
.home-v2 .ct-map iframe { display: block; width: 100%; height: 240px; border: 0; border-radius: var(--radius); background: var(--concrete-2); filter: grayscale(.4); }
.home-v2 .ct-map a {
    display: inline-flex;
    margin-top: 12px;
    padding: 6px 0;
    border-bottom: 2px solid var(--amber);
    font: 600 15px/1.2 var(--text);
    text-decoration: none;
}

/* ---------- feedback (collapsed by default) ---------- */
.home-v2 .ct-feedback-wrap { border-top: 1px solid var(--line); background: var(--concrete); }
.home-v2 .ct-feedback { scroll-margin-top: 80px; }
.home-v2 .ct-feedback > summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 6px 0;
    list-style: none;
    cursor: pointer;
}
.home-v2 .ct-feedback > summary::-webkit-details-marker { display: none; }
.home-v2 .ct-feedback > summary span { font: 700 clamp(30px, 3.4vw, 46px)/1 var(--display); }
.home-v2 .ct-feedback > summary small { flex: 1; color: var(--muted); font-size: 16px; }
.home-v2 .ct-feedback > summary::after {
    content: "+";
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    width: 44px;
    height: 44px;
    border: 1px solid var(--ink);
    border-radius: 50%;
    font: 500 26px/1 var(--text);
}
.home-v2 .ct-feedback[open] > summary::after { content: "–"; }

.home-v2 .ct-form { max-width: 880px; margin-top: 32px; }
.home-v2 .ct-hp { position: absolute; left: -9999px; width: 1px; height: 1px; opacity: 0; }
.home-v2 .ct-form fieldset { min-width: 0; border: 0; margin: 0; padding: 0; }
.home-v2 .ct-form legend { margin-bottom: 10px; padding: 0; font: 600 14px/1.2 var(--text); }

.home-v2 .ct-rating-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 8px; }
.home-v2 .ct-rating-grid label {
    position: relative;
    display: grid;
    justify-items: center;
    gap: 2px;
    padding: 12px 6px;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    cursor: pointer;
    text-align: center;
    transition: background .2s ease, color .2s ease;
}
.home-v2 .ct-rating-grid input { position: absolute; opacity: 0; pointer-events: none; }
.home-v2 .ct-rating-grid .n { font: 700 28px/1 var(--display); }
.home-v2 .ct-rating-grid strong { font: 500 13px/1.2 var(--text); }
.home-v2 .ct-rating-grid label:has(input:checked) { background: var(--ink); border-color: var(--ink); color: #fff; }
.home-v2 .ct-rating-grid label:has(input:focus-visible) { outline: 3px solid var(--amber); outline-offset: 2px; }

.home-v2 .ct-pills { display: flex; flex-wrap: wrap; gap: 8px; }
.home-v2 .ct-pills label { position: relative; display: inline-flex; }
.home-v2 .ct-pills input { position: absolute; opacity: 0; pointer-events: none; }
.home-v2 .ct-pills span {
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 999px;
    font: 500 14px/1 var(--text);
    cursor: pointer;
    transition: background .2s ease, color .2s ease;
}
.home-v2 .ct-pills label:has(input:checked) span { background: var(--ink); border-color: var(--ink); color: #fff; }
.home-v2 .ct-pills label:has(input:focus-visible) span { outline: 3px solid var(--amber); outline-offset: 2px; }

.home-v2 .ct-consent { display: flex !important; align-items: flex-start; gap: 12px; }
.home-v2 .ct-consent input { width: 20px; height: 20px; margin-top: 2px; accent-color: var(--ink); }
.home-v2 .ct-consent strong { display: block; font: 600 15px/1.3 var(--text); }
.home-v2 .ct-consent small { color: var(--muted); font-size: 14px; }

/* ---------- responsive ---------- */
@media (max-width: 900px) {
    .home-v2 .contact-layout { grid-template-columns: 1fr; }
    .home-v2 .ct-feedback > summary small { display: none; }
}
@media (max-width: 640px) {
    .home-v2 .form-grid { grid-template-columns: 1fr; }
    .home-v2 .ct-rating-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 6px; }
    .home-v2 .ct-rating-grid strong { display: none; }
    .home-v2 .contact-layout button[type="submit"],
    .home-v2 .ct-submit { width: 100%; }
}
@media (prefers-reduced-motion: reduce) {
    .home-v2 *, .home-v2 *::before, .home-v2 *::after { transition: none !important; animation: none !important; }
}
</style>

<div class="home-v2">

    {{-- HEADER --}}
    <header class="cm-head">
        <div class="ed-wrap">
            <h1 data-reveal>Tell us about your project.</h1>
            <p data-reveal>Send a brief and Leivant replies within one business day.</p>
        </div>
    </header>

    {{-- FORM + DETAILS --}}
    <section id="contact-form" class="ed-section" aria-label="Send a message">
        <div class="ed-wrap contact-layout">
            <div data-reveal>
                @include('seo.partials.inquiry-form', [
                    'formTitle' => 'Send a Project Message',
                    'defaultSubject' => 'Construction project inquiry',
                    'nextNote' => 'After submitting, Leivant reviews your scope, location, budget, and timeline before replying by your preferred contact method. We respond within one business day.',
                ])
            </div>

            <aside class="ct-info" data-reveal>
                <div class="ct-info-list">
                    <div class="ct-info-row">
                        <small>Phone</small>
                        <a href="tel:{{ config('app.company.phone_tel', '+255717970799') }}">{{ config('app.company.phone', '0717 970 799') }}</a>
                    </div>
                    <div class="ct-info-row">
                        <small>WhatsApp</small>
                        <a href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}" target="_blank" rel="noopener">Chat with us</a>
                    </div>
                    <div class="ct-info-row">
                        <small>Email</small>
                        <a href="mailto:{{ config('app.company.email', 'info@leivantconstruction.com') }}">{{ config('app.company.email', 'info@leivantconstruction.com') }}</a>
                    </div>
                    <div class="ct-info-row">
                        <small>Office</small>
                        <strong>Majumba site, Kipawa Airport, Dar es Salaam, Tanzania</strong>
                    </div>
                    <div class="ct-info-row">
                        <small>Hours</small>
                        <strong>Mon–Fri 8:00 AM–6:00 PM. Saturday by appointment.</strong>
                    </div>
                </div>
                <div class="ct-map">
                    <iframe title="Leivant Construction office map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=-6.8664952,39.1899987&z=16&output=embed"></iframe>
                    <a href="{{ config('app.company.map_url', 'https://www.google.com/maps?q=-6.8664952,39.1899987&z=17&hl=en') }}" target="_blank" rel="noopener">Open in Google Maps</a>
                </div>
            </aside>
        </div>
    </section>

    @php
        $visitReasons = ['Find construction services', 'Request a project quote', 'Browse products or equipment', 'Find providers', 'Check company information', 'General website review'];
        $feedbackAreas = ['Navigation', 'Mobile view', 'Design', 'Service details', 'Products', 'Contact forms', 'Speed', 'Trust information'];
        $selectedFeedbackAreas = (array) old('feedback_areas', []);
        $feedbackOpen = ($errors->hasBag('websiteFeedback') && $errors->getBag('websiteFeedback')->any()) || old('experience');
    @endphp

    {{-- FEEDBACK (collapsed until opened) --}}
    <section class="ed-section ct-feedback-wrap" aria-label="Website feedback">
        <div class="ed-wrap">
            <details id="website-feedback" class="ct-feedback" @if ($feedbackOpen) open @endif>
                <summary>
                    <span>Website feedback</span>
                    <small>Tell us how we can improve the site.</small>
                </summary>

                <form method="POST" action="{{ route('contact.feedback') }}" class="ct-form">
                    @csrf
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="ct-hp" aria-hidden="true">
                    <input type="hidden" name="page_url" value="{{ old('page_url', url()->current()) }}">
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
                        <fieldset class="full">
                            <legend>Overall rating *</legend>
                            <div class="ct-rating-grid">
                                @foreach ([5 => 'Excellent', 4 => 'Good', 3 => 'Average', 2 => 'Difficult', 1 => 'Poor'] as $value => $label)
                                    <label>
                                        <input type="radio" name="rating" value="{{ $value }}" @checked((string) old('rating', '5') === (string) $value) required>
                                        <span class="n">{{ $value }}</span>
                                        <strong>{{ $label }}</strong>
                                    </label>
                                @endforeach
                            </div>
                            @error('rating', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                        </fieldset>
                        <fieldset class="full">
                            <legend>Which areas should we check?</legend>
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
                            <span>Your experience *</span>
                            <textarea name="experience" required placeholder="What worked, and what slowed you down?">{{ old('experience') }}</textarea>
                            @error('experience', 'websiteFeedback') <small>{{ $message }}</small> @enderror
                        </label>
                        <label class="full ct-consent">
                            <input type="checkbox" name="contact_permission" value="1" @checked(old('contact_permission'))>
                            <span>
                                <strong>You may contact me about this feedback</strong>
                                <small>Only if Leivant needs clarification.</small>
                            </span>
                        </label>
                    </div>
                    <button class="ct-submit" type="submit">Send feedback</button>
                </form>
            </details>
        </div>
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