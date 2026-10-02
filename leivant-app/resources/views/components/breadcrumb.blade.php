@props(['items' => [], 'tone' => 'dark'])

@php
    $isLight = $tone === 'light';
    $listClass = $isLight ? 'text-zinc-700' : 'text-zinc-400';
    $separatorClass = $isLight ? 'text-zinc-500' : 'text-zinc-600';
    $currentClass = $isLight ? 'text-black' : 'text-vant-gold';
    $linkClass = $isLight ? 'hover:text-vant-orange' : 'hover:text-vant-gold';
@endphp

<nav class="vant-container breadcrumb-bar">
    <ol class="flex flex-wrap items-center gap-2 text-[#888880]">
        <li><a href="{{ route('home') }}" class="{{ $linkClass }}">Home</a></li>
        @foreach ($items as $item)
            <li class="text-[#555]">/</li>
            <li>
                @if (! empty($item['url']))
                    <a href="{{ $item['url'] }}" class="{{ $linkClass }}">{{ $item['label'] }}</a>
                @else
                    <span class="font-bold text-vant-gold">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
