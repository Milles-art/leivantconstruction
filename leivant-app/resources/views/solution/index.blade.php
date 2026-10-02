@extends('layouts.app')

@section('title', 'One Company Desk for Construction Services | Leivant')
@section('meta_description', 'Leivant coordinates construction services, equipment, labour, providers, and site support through one company-led workflow across Tanzania.')
@section('canonical', route('solution.index'))

@section('content')
    @php
        $siteImage = fn (string $name) => asset("images/site/{$name}.jpg");

        $painPoints = [
            'Clients struggle to know who is reliable before money or materials are committed.',
            'Design, labour, tools, suppliers, supervision, and delivery are often handled in separate conversations.',
            'Regional and international clients need one trusted Tanzania-based coordination point.',
            'Projects lose time when quotation, site readiness, equipment, and communication are not aligned.',
        ];

        $flow = [
            ['Request', 'Client shares the service, equipment, provider, labour, or site-support need.'],
            ['Review', 'Leivant checks project location, scope, documents, budget range, and current readiness.'],
            ['Plan', 'The right service path, team, equipment option, or provider connection is organized.'],
            ['Confirm', 'Pricing, rental dates, delivery plan, timeline, and responsibilities are clarified.'],
            ['Deliver', 'The service, equipment, team, or approved connection is coordinated to the project site.'],
            ['Follow Up', 'Progress checks and client feedback help keep the work accountable.'],
        ];

        $helpGroups = [
            [
                'title' => 'Clients',
                'body' => 'Homeowners, developers, institutions, contractors, diaspora clients, and international partners can start with one clear project brief instead of chasing many contacts.',
                'image' => $siteImage('solution-clients'),
            ],
            [
                'title' => 'Providers',
                'body' => 'Approved suppliers, engineers, architects, contractors, equipment businesses, and skilled teams gain a professional route to relevant construction requests.',
                'image' => $siteImage('international-client-support'),
            ],
            [
                'title' => 'Projects',
                'body' => 'Buildings, road works, renovations, installations, rental equipment, materials, site support, and supervision are handled with better coordination.',
                'image' => $siteImage('road-civil-works'),
            ],
        ];

        $coordination = [
            'Building construction',
            '3D design and drawings',
            'Engineering review',
            'Skilled labour',
            'Site support',
            'Equipment rental',
            'Materials support',
            'Project supervision',
            'Provider discovery',
        ];

        $trustPoints = [
            'One company communication desk',
            'Structured service request forms',
            'Leivant-reviewed providers',
            'Company-managed equipment catalog',
            'Order and inquiry tracking',
            'Tanzania presence with international-client readiness',
        ];
    @endphp

    <x-breadcrumb :items="[['label' => 'Solution']]" />

    <section class="relative overflow-hidden pb-20">
        <div class="absolute inset-x-0 top-0 h-[38rem] bg-gradient-to-b from-vant-steel/70 to-transparent"></div>
        <div class="vant-container relative">
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Leivant Solution</p>
                    <h1 class="mt-3 text-5xl font-extrabold text-white sm:text-7xl">One Company Desk For Construction Services.</h1>
                    <p class="mt-6 max-w-3xl text-lg leading-8 text-zinc-200">
                        Leivant helps clients move from a project need to organized action by coordinating services, equipment, labour, providers, site support, and follow-up through one company-led workflow.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('contact.index') }}" class="vant-button">Request Project Support</a>
                        <a href="{{ route('services.index') }}" class="vant-button-outline">Explore Services</a>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-lg border border-vant-gold/30 bg-vant-card shadow-gold">
                    <img src="{{ $siteImage('contract-execution') }}" alt="Construction planning documents and project coordination" width="900" height="600" class="h-[34rem] w-full object-cover opacity-85" loading="eager" decoding="async" fetchpriority="high">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-vant-gold">Company-led workflow</p>
                        <p class="mt-2 max-w-xl font-heading text-5xl font-extrabold text-white">Request. Review. Plan. Deliver.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="light-industrial-band py-20">
        <div class="vant-container grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-orange">The Problem</p>
                <h2 class="mt-3 text-4xl font-extrabold text-black sm:text-6xl">Construction slows down when every decision lives in a different place.</h2>
                <p class="mt-5 text-base font-semibold leading-8 text-black">
                    Leivant exists to reduce confusion before work starts and to make the next practical step easier to understand.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($painPoints as $point)
                    <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm">
                        <span class="grid h-11 w-11 place-items-center rounded-md bg-vant-orange font-heading text-2xl font-extrabold text-white">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <p class="mt-4 text-sm font-semibold leading-7 text-black">{{ $point }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="vant-container">
            <div class="max-w-4xl">
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">The Leivant Method</p>
                <h2 class="mt-3 text-4xl font-extrabold text-white sm:text-6xl">A practical route from first request to site follow-up.</h2>
            </div>

            <div class="mt-12 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                @foreach ($flow as [$title, $body])
                    <article class="group rounded-lg border border-white/10 bg-vant-card p-5 transition hover:-translate-y-1 hover:border-vant-gold hover:shadow-gold">
                        <p class="font-heading text-5xl font-extrabold {{ $loop->first ? 'text-vant-gold' : 'text-white' }}">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="mt-5 text-3xl font-extrabold text-white">{{ $title }}</h3>
                        <p class="mt-3 text-sm leading-6 text-zinc-200">{{ $body }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-20 text-black">
        <div class="vant-container">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-orange">Who It Helps</p>
                    <h2 class="mt-3 max-w-3xl text-4xl font-extrabold text-black sm:text-6xl">Built for clients, providers, and project teams.</h2>
                </div>
                <p class="max-w-2xl text-base font-semibold leading-8 text-black">
                    The solution is not just a directory or a shop. It is a coordination layer for construction decisions that need people, tools, information, and follow-up.
                </p>
            </div>

            <div class="mt-12 grid gap-6 lg:grid-cols-3">
                @foreach ($helpGroups as $group)
                    <article class="group overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-vant-gold hover:shadow-xl">
                        <img src="{{ $group['image'] }}" alt="{{ match (basename(parse_url($group['image'], PHP_URL_PATH) ?? '')) {
                            'international-client-support.jpg' => 'Leivant Construction Solutions supporting diaspora and international clients building in Tanzania',
                            'road-civil-works.jpg' => 'Civil works and road construction coordinated through Leivant Tanzania',
                            default => $group['title'],
                        } }}" width="900" height="600" class="h-64 w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
                        <div class="p-6">
                            <h3 class="text-4xl font-extrabold text-black">{{ $group['title'] }}</h3>
                            <p class="mt-4 text-sm font-semibold leading-7 text-black">{{ $group['body'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="light-industrial-band py-20">
        <div class="vant-container grid gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-orange">What Leivant Coordinates</p>
                <h2 class="mt-3 text-4xl font-extrabold text-black sm:text-6xl">The services, tools, and support around the project.</h2>
                <p class="mt-5 text-base font-semibold leading-8 text-black">
                    Leivant keeps the conversation practical: what is needed, where it is needed, who can deliver it, what it costs, and what should happen next.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($coordination as $item)
                    <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-vant-orange">Leivant Coordinates</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-black">{{ $item }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="vant-container grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Why It Works</p>
                <h2 class="mt-3 text-4xl font-extrabold text-white sm:text-6xl">Leivant gives construction requests a responsible place to land.</h2>
                <p class="mt-5 max-w-2xl text-base leading-8 text-zinc-200">
                    Instead of leaving clients to compare unverified contacts, guess equipment availability, or chase scattered updates, Leivant turns requests into trackable company communication.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($trustPoints as $point)
                    <div class="rounded-lg border border-white/10 bg-vant-card p-5">
                        <span class="inline-flex rounded-md bg-vant-gold px-3 py-1 text-xs font-black uppercase text-black">Trust</span>
                        <p class="mt-4 text-sm font-semibold leading-6 text-zinc-100">{{ $point }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="steel-band py-16">
        <div class="vant-container grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-vant-gold">Start With A Project Brief</p>
                <h2 class="mt-3 text-4xl font-extrabold text-white sm:text-5xl">Send Leivant the project location, service need, timeline, and contact details.</h2>
                <p class="mt-4 max-w-3xl text-base leading-8 text-zinc-200">
                    The company will review the request and respond with the next practical step for service, equipment, provider connection, or site support.
                </p>
            </div>
            <a href="{{ route('contact.index') }}" class="vant-button">Send Project Brief</a>
        </div>
    </section>
@endsection
