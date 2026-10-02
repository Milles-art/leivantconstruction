@props(['service'])

<article class="group overflow-hidden border border-vant-gold/20 bg-[#111111] transition duration-300 hover:-translate-y-1 hover:border-vant-gold hover:shadow-gold">
    <div class="relative h-52 overflow-hidden">
        <img src="{{ $service['image'] }}" alt="{{ match (basename(parse_url($service['image'], PHP_URL_PATH) ?? '')) {
            'hero-construction-site.jpg' => 'Active construction site supervised by Leivant Construction Solutions',
            'road-civil-works.jpg' => 'Civil works and road construction coordinated through Leivant Tanzania',
            'engineering-review.jpg' => 'Structural engineering review and site inspection by Leivant',
            'materials-supply.jpg' => 'Building materials including cement, sand, blocks, and steel available through Leivant Marketplace',
            'electrical-plumbing.jpg' => 'Power tools and electrical equipment for construction sites in Tanzania',
            'project-management.jpg' => 'Construction project management and site coordination in Tanzania',
            default => $service['name'],
        } }}" width="900" height="600" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
        <span class="absolute left-4 top-4 bg-vant-gold px-3 py-1 text-xs font-black uppercase text-black shadow-sm">
            {{ $service['provider_category'] }}
        </span>
    </div>
    <div class="p-6">
        <h3 class="text-3xl font-extrabold text-[#F8F6F0]">{{ $service['name'] }}</h3>
        <p class="mt-3 text-sm font-light leading-6 text-[#888880]">{{ $service['summary'] }}</p>
    </div>
</article>
