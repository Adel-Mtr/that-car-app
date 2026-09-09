@props(['vehicle'])

@php
    $healthTone = match(true) {
        $vehicle->health_score < 50 => ['bg-rose-100 text-rose-700', 'bg-rose-700'],
        $vehicle->health_score < 80 => ['bg-amber-100 text-amber-700', 'bg-amber-700'],
        default => ['bg-moss-100 text-moss-700', 'bg-moss-600'],
    };
    $gradient = match(strtolower((string) $vehicle->colour)) {
        'portimao blue', 'deep blue' => 'from-[#1b385b] to-[#0e1724]',
        'racing red' => 'from-[#9b2d25] to-[#3a1110]',
        'pearl white' => 'from-[#d7d9d6] to-[#777e78]',
        default => 'from-[#4a514c] to-[#171c19]',
    };
    $openItemsCount = $vehicle->open_items_count ?? 0;
@endphp

<article {{ $attributes->merge(['class' => 'group overflow-hidden rounded-card border border-ink-950/8 bg-paper shadow-card transition hover:-translate-y-1 hover:shadow-lift']) }}>
    <a href="{{ route('vehicles.show', $vehicle) }}" class="block">
        <div class="relative h-40 overflow-hidden bg-gradient-to-br {{ $gradient }} p-5 text-white">
            <div class="absolute -right-10 -top-12 size-44 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative flex items-start justify-between gap-4">
                <span class="plate rounded-md bg-[#f2d749] px-3 py-1.5 font-mono text-[0.68rem] font-bold text-ink-950">{{ $vehicle->registration }}</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/12 px-2.5 py-1 text-[0.66rem] font-semibold backdrop-blur"><span class="size-1.5 rounded-full {{ $healthTone[1] }}"></span>{{ $vehicle->health_score }} health</span>
            </div>
            <svg viewBox="0 0 440 150" class="absolute -bottom-4 left-1/2 h-31 w-[90%] -translate-x-1/2 text-white/90 transition duration-500 group-hover:translate-x-[calc(-50%+4px)]" aria-hidden="true">
                <path fill="currentColor" d="M76 105c7-25 18-47 42-58 24-11 74-18 120-16 42 2 70 11 99 35l40 11c19 5 31 18 31 35v13H37v-9c0-7 6-11 15-11h24Zm56-51c-17 7-27 19-35 37h113V48c-31-1-59 1-78 6Zm95-6v43h119c-29-27-64-40-119-43Z"/>
                <circle cx="109" cy="125" r="25" fill="#121714"/><circle cx="109" cy="125" r="13" fill="#8b928c"/><circle cx="333" cy="125" r="25" fill="#121714"/><circle cx="333" cy="125" r="13" fill="#8b928c"/>
            </svg>
        </div>
        <div class="p-5">
            <div class="flex items-start justify-between gap-4">
                <div><p class="text-xs font-medium text-ink-700/55">{{ $vehicle->year }} · {{ $vehicle->fuel_type }}</p><h3 class="mt-1 text-lg font-semibold tracking-tight">{{ $vehicle->make }} {{ $vehicle->model }}</h3><p class="mt-0.5 text-sm text-ink-700/60">{{ $vehicle->variant }}</p></div>
                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-sand-100 text-ink-700 transition group-hover:bg-ink-950 group-hover:text-white"><x-icon name="chevron-right" class="size-4" /></span>
            </div>
            <div class="mt-5 flex items-center justify-between border-t border-ink-950/8 pt-4 text-xs text-ink-700/60"><span>{{ number_format($vehicle->current_mileage) }} mi</span><span>{{ $openItemsCount }} open {{ str('item')->plural($openItemsCount) }}</span></div>
        </div>
    </a>
</article>
