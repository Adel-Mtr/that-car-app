@props(['specialist'])

<article {{ $attributes->merge(['class' => 'group rounded-card border border-ink-950/8 bg-paper p-5 shadow-card transition hover:-translate-y-1']) }}>
    <a href="{{ route('specialists.show', $specialist) }}" class="block">
        <div class="flex items-start justify-between gap-4"><span class="grid size-12 place-items-center rounded-2xl bg-ink-950 text-lime-400"><x-icon name="wrench" /></span><div class="flex items-center gap-1 text-xs font-semibold"><span class="text-amber-700">★</span> {{ $specialist->rating }} <span class="font-normal text-ink-700/40">({{ number_format($specialist->review_count) }})</span></div></div>
        <div class="mt-6 flex items-center gap-2">@if($specialist->is_verified)<span class="inline-flex items-center gap-1 rounded-full bg-moss-100 px-2.5 py-1 text-[0.63rem] font-semibold text-moss-700"><x-icon name="shield" class="size-3" /> Verified</span>@endif<span class="text-[0.65rem] text-ink-700/45">{{ $specialist->price_level }}</span></div>
        <h3 class="mt-3 text-lg font-semibold tracking-tight group-hover:text-moss-600">{{ $specialist->name }}</h3><p class="mt-2 line-clamp-2 text-sm leading-6 text-ink-700/55">{{ $specialist->tagline }}</p>
        <div class="mt-5 flex flex-wrap gap-1.5">@foreach(array_slice($specialist->categories, 0, 3) as $category)<span class="rounded-full bg-canvas px-2.5 py-1 text-[0.63rem] font-medium text-ink-700">{{ str($category)->headline() }}</span>@endforeach</div>
        <div class="mt-5 flex items-center justify-between border-t border-ink-950/8 pt-4 text-xs text-ink-700/50"><span class="flex items-center gap-1.5"><x-icon name="map-pin" class="size-3.5" /> {{ $specialist->city }}</span><span class="font-semibold text-moss-600">View specialist →</span></div>
    </a>
</article>
