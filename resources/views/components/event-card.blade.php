@props(['event'])

@php
    $tone = match($event->category) {
        'track' => 'from-[#7e2b22] to-[#251210]',
        'drive' => 'from-[#275a51] to-[#11241f]',
        'classic' => 'from-[#76613b] to-[#2d2518]',
        'show' => 'from-[#413b70] to-[#19172d]',
        default => 'from-[#315744] to-[#12241b]',
    };
@endphp

<article {{ $attributes->merge(['class' => 'group overflow-hidden rounded-card border border-ink-950/8 bg-paper shadow-card transition hover:-translate-y-1']) }}>
    <a href="{{ route('events.show', $event) }}" class="block">
        <div class="relative h-40 overflow-hidden bg-gradient-to-br {{ $tone }} p-5 text-white">
            <div class="absolute -right-8 -top-8 size-36 rounded-full border-[24px] border-white/5"></div>
            <div class="absolute bottom-3 right-5 font-mono text-[4.5rem] font-bold leading-none tracking-[-0.1em] text-white/7">{{ $event->starts_at->format('d') }}</div>
            <div class="relative flex items-start justify-between"><span class="rounded-full bg-white/10 px-3 py-1.5 text-[0.65rem] font-semibold uppercase tracking-wider text-white/80 backdrop-blur">{{ $event->category }}</span>@if($event->is_featured)<span class="inline-flex items-center gap-1 text-[0.65rem] font-semibold text-lime-300"><x-icon name="spark" class="size-3" /> Featured</span>@endif</div>
            <div class="absolute bottom-5 left-5"><p class="text-2xl font-semibold">{{ $event->starts_at->format('d') }}</p><p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/50">{{ $event->starts_at->format('M Y') }}</p></div>
        </div>
        <div class="p-5"><h3 class="text-lg font-semibold tracking-tight group-hover:text-moss-600">{{ $event->title }}</h3><p class="mt-2 line-clamp-2 text-sm leading-6 text-ink-700/55">{{ $event->summary }}</p><div class="mt-5 flex items-center justify-between border-t border-ink-950/8 pt-4 text-xs text-ink-700/50"><span class="flex min-w-0 items-center gap-1.5 truncate"><x-icon name="map-pin" class="size-3.5 shrink-0" /> {{ $event->city }}</span><span>{{ $event->price_pence ? '£'.number_format($event->price_pence / 100, 2) : 'Free' }}</span></div></div>
    </a>
</article>
