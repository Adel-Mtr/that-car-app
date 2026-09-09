@extends('layouts.marketing', ['title' => config('app.name').' — know what your car needs next'])

@section('content')
<section class="relative overflow-hidden bg-paper">
    <div class="absolute -right-24 top-20 size-96 rounded-full bg-lime-400/35 blur-3xl"></div>
    <div class="absolute -left-28 bottom-0 size-80 rounded-full bg-moss-100 blur-3xl"></div>
    <div class="relative mx-auto grid max-w-7xl gap-12 px-5 py-16 lg:grid-cols-[1.02fr_.98fr] lg:items-center lg:px-8 lg:py-24">
        <div class="max-w-2xl">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-ink-950/10 bg-white px-3.5 py-2 text-xs font-semibold text-ink-700 shadow-sm">
                <span class="size-2 rounded-full bg-moss-600"></span>
                Built for every driver. Detailed enough for enthusiasts.
            </div>
            <h1 class="text-balance text-5xl font-semibold leading-[0.98] tracking-[-0.055em] text-ink-950 sm:text-6xl lg:text-[5rem]">Know what your car needs <span class="text-moss-600">before it asks.</span></h1>
            <p class="mt-7 max-w-xl text-lg leading-8 text-ink-700">{{ config('app.name') }} turns MOT history, maintenance records and ownership dates into one clear plan — then preserves the story and proof behind every car.</p>
            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-ink-950 px-6 py-3.5 text-sm font-semibold text-white shadow-lift transition hover:-translate-y-0.5 hover:bg-ink-800">Start your garage <x-icon name="arrow-right" class="size-4" /></a>
                <a href="#how-it-works" class="inline-flex items-center justify-center rounded-full border border-ink-950/12 bg-white px-6 py-3.5 text-sm font-semibold text-ink-800 transition hover:border-ink-950/25">See how it works</a>
            </div>
            <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-xs font-medium text-ink-700/70">
                <span class="flex items-center gap-2"><x-icon name="check" class="size-4 text-moss-600" /> MOT and tax plan</span>
                <span class="flex items-center gap-2"><x-icon name="check" class="size-4 text-moss-600" /> Verified history</span>
                <span class="flex items-center gap-2"><x-icon name="check" class="size-4 text-moss-600" /> Events and specialists</span>
            </div>
        </div>

        <div class="relative mx-auto w-full max-w-xl">
            <div class="absolute inset-x-8 -bottom-8 h-20 rounded-full bg-ink-950/15 blur-2xl"></div>
            <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-canvas p-3 shadow-lift sm:p-5">
                <div class="rounded-[1.45rem] bg-ink-950 p-5 text-white sm:p-7">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/45">Your garage</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight">2021 BMW M340i</h2>
                            <span class="plate mt-3 inline-flex rounded-md bg-[#f2d749] px-3 py-1.5 font-mono text-xs font-bold text-ink-950">RJ21 MOT</span>
                        </div>
                        <div class="grid size-21 place-items-center rounded-full border-[7px] border-lime-400/20 bg-white/7">
                            <div class="text-center"><p class="text-2xl font-semibold text-lime-400">82</p><p class="text-[0.58rem] uppercase tracking-wider text-white/45">health</p></div>
                        </div>
                    </div>
                    <div class="mt-8 grid grid-cols-3 gap-2.5">
                        <div class="rounded-2xl bg-white/8 p-3.5"><p class="text-[0.65rem] text-white/45">MOT</p><p class="mt-1 text-sm font-semibold">54 days</p></div>
                        <div class="rounded-2xl bg-white/8 p-3.5"><p class="text-[0.65rem] text-white/45">Tax</p><p class="mt-1 text-sm font-semibold">Taxed</p></div>
                        <div class="rounded-2xl bg-white/8 p-3.5"><p class="text-[0.65rem] text-white/45">Mileage</p><p class="mt-1 text-sm font-semibold">38,420</p></div>
                    </div>
                </div>
                <div class="grid gap-3 p-2 pt-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-3"><span class="grid size-9 place-items-center rounded-xl bg-amber-100 text-amber-700"><x-icon name="wrench" class="size-4" /></span><div><p class="text-sm font-semibold">Tyre advisory</p><p class="mt-1 text-xs leading-5 text-ink-700/65">Plan an inspection in the next 16 days.</p></div></div>
                    </div>
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-3"><span class="grid size-9 place-items-center rounded-xl bg-moss-100 text-moss-700"><x-icon name="document" class="size-4" /></span><div><p class="text-sm font-semibold">History protected</p><p class="mt-1 text-xs leading-5 text-ink-700/65">8 records with supporting evidence.</p></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="border-y border-ink-950/8 bg-canvas py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-moss-600">One living vehicle record</p>
            <h2 class="mt-4 text-balance text-4xl font-semibold tracking-[-0.045em] text-ink-950 sm:text-5xl">From annual admin to the story behind the car.</h2>
        </div>
        <div class="mt-12 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach([
                ['spark', 'A plan, not a data dump', 'MOT advisories become clear tasks with urgency, dates and the next sensible action.'],
                ['shield', 'Proof that travels with the car', 'Services, parts, invoices and verified work build a transferable vehicle passport.'],
                ['wrench', 'From issue to booked work', 'Find a relevant specialist, request work and keep the completion record automatically.'],
                ['users', 'A garage worth sharing', 'Keep a private family car private, or publish a polished build and history for enthusiasts.'],
                ['calendar', 'The right events nearby', 'Discover meets, shows, track evenings and drives matched to your interests.'],
                ['document', 'Every document in reach', 'Store insurance, service invoices and warranty files against the moment they belong to.'],
            ] as [$icon, $title, $copy])
                <article class="group rounded-card border border-ink-950/8 bg-paper p-6 shadow-card transition hover:-translate-y-1 hover:border-ink-950/15">
                    <span class="grid size-11 place-items-center rounded-2xl bg-ink-950 text-lime-400 transition group-hover:rotate-3"><x-icon :name="$icon" /></span>
                    <h3 class="mt-6 text-lg font-semibold tracking-tight">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-ink-700/70">{{ $copy }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="how-it-works" class="bg-paper py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="grid gap-14 lg:grid-cols-[.8fr_1.2fr] lg:items-start">
            <div class="lg:sticky lg:top-28">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-moss-600">How it works</p>
                <h2 class="mt-4 text-4xl font-semibold tracking-[-0.045em] sm:text-5xl">Three steps to a calmer kind of ownership.</h2>
                <p class="mt-5 text-base leading-7 text-ink-700/70">Start with the registration. {{ config('app.name') }} creates the structure; you build the trusted record over time.</p>
            </div>
            <ol class="flex flex-col gap-4">
                @foreach([
                    ['01', 'Add the car', 'Enter the registration and your key legal dates, MOT history and basic vehicle details fall into place.'],
                    ['02', 'See what matters next', 'A single health view prioritises legal dates, advisories and maintenance without unnecessary alarms.'],
                    ['03', 'Act and preserve the proof', 'Complete work yourself or through a specialist, attach evidence and keep the value in the history.'],
                ] as [$number, $title, $copy])
                    <li class="grid gap-5 rounded-card border border-ink-950/8 bg-canvas p-6 sm:grid-cols-[4rem_1fr] sm:p-8">
                        <span class="font-mono text-sm font-bold text-moss-600">{{ $number }}</span>
                        <div><h3 class="text-xl font-semibold tracking-tight">{{ $title }}</h3><p class="mt-2 max-w-xl text-sm leading-6 text-ink-700/70">{{ $copy }}</p></div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

<section id="community" class="overflow-hidden bg-ink-950 py-20 text-white lg:py-28">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl"><p class="text-xs font-semibold uppercase tracking-[0.18em] text-lime-400">Beyond the paperwork</p><h2 class="mt-4 text-4xl font-semibold tracking-[-0.045em] sm:text-5xl">Enjoy the reason you own it.</h2></div>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-lime-400">Join the community <x-icon name="arrow-right" class="size-4" /></a>
        </div>
        <div class="mt-12 grid gap-4 lg:grid-cols-3">
            @forelse($events as $event)
                <article class="rounded-card border border-white/10 bg-white/6 p-5">
                    <div class="flex items-center justify-between"><span class="rounded-full bg-lime-400/12 px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-wider text-lime-300">{{ $event->category }}</span><span class="text-xs text-white/45">{{ $event->starts_at->format('j M') }}</span></div>
                    <h3 class="mt-8 text-xl font-semibold">{{ $event->title }}</h3>
                    <p class="mt-2 flex items-center gap-2 text-sm text-white/50"><x-icon name="map-pin" class="size-4" /> {{ $event->venue }}, {{ $event->city }}</p>
                </article>
            @empty
                <p class="text-white/50">Events will appear here.</p>
            @endforelse
        </div>
        <div class="mt-12 grid gap-4 sm:grid-cols-3">
            @foreach($specialists as $specialist)
                <div class="rounded-2xl border border-white/10 p-5"><div class="flex items-center gap-2 text-lime-400"><x-icon name="shield" class="size-4" /><span class="text-xs font-semibold">Verified specialist</span></div><p class="mt-4 font-semibold">{{ $specialist->name }}</p><p class="mt-1 text-sm text-white/45">{{ $specialist->city }} · {{ $specialist->rating }} ★</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-lime-400 py-16">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 px-5 sm:flex-row sm:items-center sm:justify-between lg:px-8">
        <div><p class="text-sm font-semibold text-moss-700">Your first vehicle is free.</p><h2 class="mt-2 text-3xl font-semibold tracking-[-0.04em] text-ink-950 sm:text-4xl">Start with the car outside.</h2></div>
        <a href="{{ route('register') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-ink-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-ink-800">Create your garage <x-icon name="arrow-right" class="size-4" /></a>
    </div>
</section>
@endsection
