@props(['provider'])

@php
    $category = strtolower($provider['category'] ?? '');
    $image = match (true) {
        str_contains($category, 'architect') => 'architecture-drawings.jpg',
        str_contains($category, 'engineer') => 'engineering-review.jpg',
        str_contains($category, 'material') || str_contains($category, 'supplier') => 'materials-supply.jpg',
        str_contains($category, 'equipment') => 'site-support.jpg',
        str_contains($category, 'electrical') || str_contains($category, 'plumbing') => 'electrical-plumbing.jpg',
        str_contains($category, 'labour') || str_contains($category, 'skilled') => 'skilled-labour.jpg',
        str_contains($category, 'transport') || str_contains($category, 'logistics') => 'road-civil-works.jpg',
        str_contains($category, 'contractor') => 'contract-execution.jpg',
        default => 'project-management.jpg',
    };

    $imageAlt = match ($image) {
        'architecture-drawings.jpg' => 'Architectural drawings and construction planning documents',
        'engineering-review.jpg' => 'Structural engineering review and site inspection by Leivant',
        'materials-supply.jpg' => 'Building materials including cement, sand, blocks, and steel available through Leivant Marketplace',
        'site-support.jpg' => 'Heavy equipment and site machinery available for rent through Leivant Tanzania',
        'electrical-plumbing.jpg' => 'Power tools and electrical equipment for construction sites in Tanzania',
        'skilled-labour.jpg' => 'Skilled construction labour and trade teams in Tanzania',
        'road-civil-works.jpg' => 'Civil works and road construction coordinated through Leivant Tanzania',
        'contract-execution.jpg' => 'Construction project execution and supervised site delivery',
        default => 'Construction project management and site coordination in Tanzania',
    };
@endphp

<article class="pv">
    <img src="{{ asset('images/site/'.$image) }}" alt="{{ $imageAlt }}" loading="lazy" decoding="async">

    <div class="pv-body">
        <div class="pv-meta">
            <span class="cm-tag">{{ $provider['category'] }}</span>
            <strong class="pv-rate">{{ number_format((float) $provider['rating'], 1) }}</strong>
        </div>
        <div>
            <h3>{{ $provider['name'] }}</h3>
            <p class="pv-sum">{{ $provider['summary'] }}</p>
        </div>

        <div class="pv-facts">
            <span>{{ $provider['region'] }}</span>
            @if (! empty($provider['location']))
                <span>{{ $provider['location'] }}</span>
            @endif
            <span>{{ $provider['jobs'] }} jobs</span>
            <span>{{ ($provider['is_verified'] ?? false) ? 'Verified' : 'Leivant Review' }}</span>
            <span>{{ $provider['response'] ?? 'Connection by request' }}</span>
        </div>

        <a href="{{ config('app.company.whatsapp') }}?text={{ urlencode('Hello Leivant, connect me directly with '.$provider['name'].' in '.$provider['region']) }}" class="pv-act">Send Request</a>
    </div>
</article>
