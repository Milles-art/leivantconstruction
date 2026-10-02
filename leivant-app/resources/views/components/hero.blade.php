<section class="relative -mt-[68px] min-h-screen overflow-hidden bg-vant-black pt-[68px]">
    <div class="absolute inset-0">
        <img src="{{ asset('images/site/hero-construction-site.jpg') }}" alt="Active construction site supervised by Leivant Construction Solutions" width="1100" height="733" class="h-full w-full object-cover opacity-40" loading="eager" decoding="async" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-r from-vant-black via-vant-black/82 to-vant-steel/45"></div>
        <div class="absolute inset-0 concrete-overlay"></div>
    </div>

    <div class="vant-container relative grid min-h-[calc(100vh-5rem)] items-center gap-12 py-16 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
            <div class="section-label">Est. Tanzania - East Africa</div>
            <h1 class="mt-6 max-w-4xl text-5xl font-extrabold leading-[0.92] text-white sm:text-7xl lg:text-8xl">
                Construction Solutions Built For <span class="text-vant-gold">Projects</span> Across Borders
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-zinc-200">
                Leivant Construction Solutions Company supports clients, developers, contractors, and project teams with design coordination, engineering support, construction delivery, labour planning, site supervision, and practical project support across Tanzania and beyond.
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('services.index') }}" class="vant-button">Explore Services</a>
                <a href="{{ route('contact.index') }}" class="vant-button-outline bg-black/30 backdrop-blur">Request Consultation</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
            <div class="rounded-lg border border-vant-gold/40 bg-vant-gold/15 p-6 backdrop-blur">
                <p class="text-sm font-bold uppercase tracking-wide text-vant-gold">Company Services</p>
                <p class="mt-3 font-heading text-4xl font-extrabold">Design to delivery</p>
                <p class="mt-2 text-sm leading-6 text-zinc-200">Architecture, engineering, construction, and labour coordination under one company-led workflow.</p>
            </div>
            <div class="rounded-lg border border-vant-orange/40 bg-vant-orange/15 p-6 backdrop-blur">
                <p class="text-sm font-bold uppercase tracking-wide text-orange-200">Project Reach</p>
                <p class="mt-3 font-heading text-4xl font-extrabold">Local and international</p>
                <p class="mt-2 text-sm leading-6 text-zinc-200">Support for clients working in Tanzania, across the region, and with international project partners.</p>
            </div>
            <div class="rounded-lg border border-vant-green/40 bg-vant-green/15 p-6 backdrop-blur xl:col-span-2">
                <p class="text-sm font-bold uppercase tracking-wide text-emerald-200">Project Support</p>
                <p class="mt-3 font-heading text-4xl font-extrabold">Planning, people, tools</p>
                <p class="mt-2 text-sm leading-6 text-zinc-200">A single company contact for technical planning, labour coordination, construction tools, materials, and site follow-up.</p>
            </div>
        </div>
    </div>
</section>
