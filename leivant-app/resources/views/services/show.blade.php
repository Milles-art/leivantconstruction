@extends('layouts.seo')

@section('title', $service['name'].' Tanzania | Leivant Construction Services')
@section('meta_description', $service['summary'] ?? 'Professional construction services, planning, BOQ support, project coordination, and provider matching in Tanzania.')
@section('canonical', route('services.show', $service['slug']))
@section('og_image', $service['image'])

@section('content')
    <section class="hero">
        <div class="hero-media">
            <img src="{{ $service['image'] }}" alt="{{ match (basename(parse_url($service['image'], PHP_URL_PATH) ?? '')) {
                'hero-construction-site.jpg' => 'Active construction site supervised by Leivant Construction Solutions',
                'road-civil-works.jpg' => 'Civil works and road construction coordinated through Leivant Tanzania',
                'engineering-review.jpg' => 'Structural engineering review and site inspection by Leivant',
                'materials-supply.jpg' => 'Building materials including cement, sand, blocks, and steel available through Leivant Marketplace',
                'electrical-plumbing.jpg' => 'Power tools and electrical equipment for construction sites in Tanzania',
                'project-management.jpg' => 'Construction project management and site coordination in Tanzania',
                default => $service['name'],
            } }}" width="1600" height="1000" loading="eager" decoding="async" fetchpriority="high">
        </div>
        <div class="container hero-inner">
            <div>
                <p class="eyebrow">{{ $service['provider_category'] }}</p>
                <h1>{{ $service['name'] }}</h1>
                <p class="lead">{{ $service['description'] }}</p>
                <div class="hero-actions">
                    <a href="#service-request" class="btn btn-primary">Submit Service Request</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline">All Services</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-paper">
        <div class="container">
            <div class="grid grid-2">
                <article class="card card-pad">
                    <p class="eyebrow">What Leivant Delivers</p>
                    <h2 style="margin-top:8px;">Clear service output.</h2>
                    <div class="grid" style="margin-top:22px;">
                        @foreach ($service['deliverables'] ?? $service['features'] as $item)
                            <div class="notice">{{ $item }}</div>
                        @endforeach
                    </div>
                </article>

                <article class="card card-pad">
                    <p class="eyebrow">Best For</p>
                    <h2 style="margin-top:8px;">Where this service fits.</h2>
                    <div class="feature-list" style="margin-top:22px;">
                        @foreach ($service['best_for'] ?? [] as $item)
                            <span>{{ $item }}</span>
                        @endforeach
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="split" style="align-items:start;">
                <div>
                    <div class="section-head">
                        <p class="eyebrow">Service Process</p>
                        <h2>How Leivant handles the request.</h2>
                        <p>Every request is reviewed around site location, stage, urgency, available documents, and expected outcome before a professional next step is recommended.</p>
                    </div>
                    <div class="grid grid-2 process">
                        @foreach ($service['process'] ?? [] as $step)
                            <article class="card">
                                <p>{{ $step }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>

                <aside id="service-request" class="form-card">
                    <p class="eyebrow">Request {{ $service['name'] }}</p>
                    <h2 style="margin:8px 0 0;">Send Leivant the project brief.</h2>
                    <p class="muted" style="line-height:1.7;">No account is required. Leivant Construction Desk will review the details and respond through your preferred contact channel.</p>

                    @if ($errors->any())
                        <div class="notice danger" style="margin-top:18px;">
                            <strong>Please check the form.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('services.request', $service['slug']) }}" class="form-grid" style="margin-top:22px;">
                        @csrf
                        <label>
                            <span>Full Name</span>
                            <input name="name" value="{{ old('name') }}" required>
                        </label>
                        <label>
                            <span>Company / Client Name</span>
                            <input name="company" value="{{ old('company') }}">
                        </label>
                        <label>
                            <span>Phone / WhatsApp</span>
                            <input name="phone" value="{{ old('phone') }}" required>
                        </label>
                        <label>
                            <span>Email</span>
                            <input type="email" name="email" value="{{ old('email') }}">
                        </label>
                        <label>
                            <span>Region</span>
                            <select name="region" required>
                                <option value="">Select region</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}" @selected(old('region') === $region)>{{ $region }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Site Location</span>
                            <input name="site_location" value="{{ old('site_location') }}" placeholder="Example: Salasala, Kinondoni" required>
                        </label>
                        <label>
                            <span>Project Type</span>
                            <select name="project_type" required>
                                <option value="">Select project type</option>
                                @foreach ($service['project_types'] ?? [$service['name']] as $type)
                                    <option value="{{ $type }}" @selected(old('project_type') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Project Stage</span>
                            <select name="project_stage">
                                <option value="">Select stage</option>
                                @foreach ($requestOptions['stages'] as $stage)
                                    <option value="{{ $stage }}" @selected(old('project_stage') === $stage)>{{ $stage }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Budget Range</span>
                            <select name="budget_range">
                                <option value="">Select budget range</option>
                                @foreach ($requestOptions['budget_ranges'] as $range)
                                    <option value="{{ $range }}" @selected(old('budget_range') === $range)>{{ $range }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Timeline</span>
                            <select name="timeline" required>
                                <option value="">Select timeline</option>
                                @foreach ($requestOptions['timelines'] as $timeline)
                                    <option value="{{ $timeline }}" @selected(old('timeline') === $timeline)>{{ $timeline }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="full">
                            <span>Preferred Contact</span>
                            <select name="preferred_contact" required>
                                @foreach (['whatsapp' => 'WhatsApp', 'phone' => 'Phone Call', 'email' => 'Email'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('preferred_contact', 'whatsapp') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="full">
                            <span>Project Message</span>
                            <textarea name="message" required placeholder="Describe the project, site condition, expected work, or challenge Leivant should review.">{{ old('message') }}</textarea>
                        </label>
                        <button class="btn btn-primary full" type="submit">Submit Service Request</button>
                    </form>
                </aside>
            </div>
        </div>
    </section>
@endsection
