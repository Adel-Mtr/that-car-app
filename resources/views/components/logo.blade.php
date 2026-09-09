@props(['compact' => false, 'inverse' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <span class="grid size-9 place-items-center rounded-xl {{ $inverse ? 'bg-lime-400 text-ink-950' : 'bg-ink-950 text-lime-400' }} shadow-sm">
        <svg viewBox="0 0 32 32" class="size-5" aria-hidden="true">
            <path fill="currentColor" d="M7.2 18.3 9.6 12a3 3 0 0 1 2.8-1.9h7.2a3 3 0 0 1 2.8 1.9l2.4 6.3a3.7 3.7 0 0 1 2.2 3.4v2.1a1.6 1.6 0 0 1-1.6 1.6h-1.2a1.6 1.6 0 0 1-1.6-1.6v-.4H9.4v.4a1.6 1.6 0 0 1-1.6 1.6H6.6A1.6 1.6 0 0 1 5 23.8v-2.1a3.7 3.7 0 0 1 2.2-3.4Zm3.1-.7h11.4l-1.7-4.4a.8.8 0 0 0-.8-.5h-6.4a.8.8 0 0 0-.8.5l-1.7 4.4Zm-.7 4.2a1.4 1.4 0 1 0 0-2.8 1.4 1.4 0 0 0 0 2.8Zm12.8 0a1.4 1.4 0 1 0 0-2.8 1.4 1.4 0 0 0 0 2.8Z"/>
            <path fill="currentColor" d="M23.2 6.4 25 4.6l1.8 1.8L25 8.2zM15 4h2.5v3H15zM5.2 6.4 7 4.6l1.8 1.8L7 8.2z" opacity=".75"/>
        </svg>
    </span>
    @unless($compact)
        <span class="text-[1.05rem] font-semibold tracking-[-0.03em] {{ $inverse ? 'text-white' : 'text-ink-950' }}">{{ config('app.name') }}</span>
    @endunless
</span>
