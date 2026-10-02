@props(['stats' => []])

<section class="steel-band">
    <div class="vant-container grid gap-4 py-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($stats as $stat)
            @php
                $tone = [
                    'gold' => 'border-vant-gold/40 bg-vant-gold/10 text-vant-gold',
                    'orange' => 'border-vant-orange/40 bg-vant-orange/10 text-orange-200',
                    'green' => 'border-vant-green/40 bg-vant-green/10 text-emerald-200',
                    'steel' => 'border-sky-400/30 bg-sky-400/10 text-sky-100',
                ][$stat['tone'] ?? 'gold'] ?? 'border-vant-gold/40 bg-vant-gold/10 text-vant-gold';
            @endphp
            <div class="rounded-lg border p-5 {{ $tone }}">
                <p class="font-heading text-4xl font-extrabold">{{ $stat['value'] }}</p>
                <p class="mt-1 text-sm font-bold uppercase tracking-wide text-white/80">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>
</section>
