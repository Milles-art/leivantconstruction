<footer class="footer-pro border-t border-white/10">
    <div class="vant-container grid gap-10 py-14 lg:grid-cols-[1.2fr_0.7fr_0.7fr_0.9fr]">
        <div>
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-vant-gold font-heading text-xl font-black text-white">L</span>
                <div>
                    <p class="font-heading text-2xl text-white">Leivant</p>
                    <p class="-mt-1 text-[10px] font-bold uppercase tracking-[0.22em] text-white/45">Construction Solutions</p>
                </div>
            </div>
            <p class="mt-6 max-w-md text-sm leading-7 text-white/62">
                Leivant Construction Solutions is a construction company in Dar es Salaam, Tanzania. We design, build, renovate, and supervise construction projects.
            </p>
        </div>

        <div>
            <h3 class="text-xs font-bold uppercase tracking-[0.28em] text-vant-gold">Services</h3>
            <div class="mt-5 grid gap-3 text-sm text-white/70">
                <span>Architecture</span>
                <span>Engineering</span>
                <span>Construction</span>
                <span>Skilled Labour</span>
                <span>Site Support</span>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold uppercase tracking-[0.28em] text-vant-gold">Company</h3>
            <div class="mt-5 grid gap-3 text-sm text-white/70">
                <a class="hover:text-vant-gold" href="{{ route('services.index') }}">Services</a>
                <a class="hover:text-vant-gold" href="{{ route('products.index') }}">Products</a>
                <a class="hover:text-vant-gold" href="{{ route('solution.index') }}">Solution</a>
                <a class="hover:text-vant-gold" href="{{ route('contact.index') }}">Contact</a>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold uppercase tracking-[0.28em] text-vant-gold">Contact</h3>
            <div class="mt-5 grid gap-3 text-sm text-white/70">
                <p>{{ config('app.company.location') }}</p>
                <a class="hover:text-vant-gold" href="tel:{{ config('app.company.phone_tel', '+255717970799') }}">{{ config('app.company.phone') }}</a>
                <a class="hover:text-vant-gold" href="{{ config('app.company.whatsapp', 'https://wa.me/255717970799') }}">WhatsApp Leivant</a>
                <div class="mt-3 flex gap-4 text-xs uppercase tracking-wide text-white/35">
                    <a class="hover:text-vant-gold" href="{{ route('privacy') }}">Privacy</a>
                    <a class="hover:text-vant-gold" href="{{ route('terms') }}">Terms</a>
                </div>
                <p>Leivant Construction Desk</p>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10 py-5">
        <div class="vant-container flex flex-col gap-3 text-xs uppercase tracking-[0.12em] text-white/35 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} Leivant Construction Solutions Company.</p>
            <p>Construction services, project delivery, and nationwide provider connections.</p>
        </div>
    </div>
</footer>
