@extends('layouts.app', ['title' => 'Overview', 'header' => 'Your garage'])

@section('content')
<div class="mx-auto max-w-[92rem]">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-sm font-medium text-moss-600">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ str(auth()->user()->name)->before(' ') }}.</p><h1 class="mt-1 text-3xl font-semibold tracking-[-0.04em] sm:text-4xl">Everything that needs your attention.</h1></div>
        @if($vehicles->isNotEmpty())<p class="mt-2 text-sm text-ink-700/55 sm:mt-0">{{ $vehicles->count() }} {{ str('vehicle')->plural($vehicles->count()) }} · {{ $vehicles->sum('open_items_count') }} open items</p>@endif
    </div>

    @if($vehicles->isEmpty())
        <section class="mt-8 overflow-hidden rounded-[2rem] bg-ink-950 p-7 text-white shadow-lift sm:p-10">
            <div class="max-w-xl"><span class="inline-flex rounded-full bg-lime-400/12 px-3 py-1.5 text-xs font-semibold text-lime-300">Your garage is ready</span><h2 class="mt-6 text-4xl font-semibold tracking-[-0.045em]">Add the car outside.</h2><p class="mt-4 text-base leading-7 text-white/60">Enter its registration and {{ config('app.name') }} will prepare the legal dates, MOT history and first maintenance plan.</p><a href="{{ route('vehicles.create') }}" class="mt-8 inline-flex items-center gap-2 rounded-full bg-lime-400 px-6 py-3.5 text-sm font-semibold text-ink-950">Add my first car <x-icon name="arrow-right" class="size-4" /></a></div>
        </section>
    @else
        @php $primaryVehicle = $vehicles->first(); @endphp
        <div class="mt-8 grid gap-5 xl:grid-cols-[1.35fr_.65fr]">
            <section class="relative overflow-hidden rounded-[2rem] bg-ink-950 p-6 text-white shadow-card sm:p-8">
                <div class="absolute right-0 top-0 h-full w-1/2 bg-[radial-gradient(circle_at_center,#d6fa7025,transparent_68%)]"></div>
                <div class="relative grid gap-8 md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <div class="flex flex-wrap items-center gap-3"><span class="plate rounded-md bg-[#f2d749] px-3 py-1.5 font-mono text-xs font-bold text-ink-950">{{ $primaryVehicle->registration }}</span><span class="text-xs font-medium text-white/45">{{ $primaryVehicle->year }} {{ $primaryVehicle->make }}</span></div>
                        <h2 class="mt-5 text-3xl font-semibold tracking-tight">{{ $primaryVehicle->model }} <span class="font-normal text-white/45">{{ $primaryVehicle->variant }}</span></h2>
                        <div class="mt-7 flex flex-wrap gap-2.5">
                            <span class="rounded-full bg-white/8 px-3.5 py-2 text-xs"><span class="text-white/45">MOT</span> · {{ $primaryVehicle->mot_due_at?->diffForHumans() ?? 'Not set' }}</span>
                            <span class="rounded-full bg-white/8 px-3.5 py-2 text-xs"><span class="text-white/45">Tax</span> · {{ str($primaryVehicle->tax_status)->headline() }}</span>
                            <span class="rounded-full bg-white/8 px-3.5 py-2 text-xs"><span class="text-white/45">Mileage</span> · {{ number_format($primaryVehicle->current_mileage) }}</span>
                        </div>
                        <a href="{{ route('vehicles.show', $primaryVehicle) }}" class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-lime-300">Open vehicle plan <x-icon name="arrow-right" class="size-4" /></a>
                    </div>
                    <div class="grid size-34 place-items-center rounded-full border-[10px] {{ $primaryVehicle->health_score >= 80 ? 'border-lime-400/20' : 'border-amber-100/20' }} bg-white/6"><div class="text-center"><p class="text-4xl font-semibold {{ $primaryVehicle->health_score >= 80 ? 'text-lime-400' : 'text-amber-100' }}">{{ $primaryVehicle->health_score }}</p><p class="mt-1 text-[0.65rem] font-semibold uppercase tracking-[0.13em] text-white/40">Car health</p></div></div>
                </div>
            </section>

            <section class="rounded-card border border-ink-950/8 bg-paper p-5 shadow-card sm:p-6">
                <div class="flex items-center justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.13em] text-ink-700/45">Next up</p><h2 class="mt-1 text-lg font-semibold">Dates to keep</h2></div><span class="grid size-10 place-items-center rounded-2xl bg-amber-100 text-amber-700"><x-icon name="bell" class="size-4.5" /></span></div>
                <div class="mt-5 flex flex-col divide-y divide-ink-950/8">
                    @forelse($reminders->take(3) as $reminder)
                        <a href="{{ route('vehicles.show', $reminder->vehicle) }}" class="flex items-center gap-3 py-3.5 first:pt-0 last:pb-0"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-canvas text-xs font-bold text-ink-700">{{ $reminder->due_at->format('d') }}<span class="-mt-1 text-[0.52rem] uppercase">{{ $reminder->due_at->format('M') }}</span></span><span class="min-w-0 flex-1"><span class="block truncate text-sm font-semibold">{{ $reminder->title }}</span><span class="mt-0.5 block truncate text-xs text-ink-700/50">{{ $reminder->vehicle->make }} {{ $reminder->vehicle->model }}</span></span><x-icon name="chevron-right" class="size-4 text-ink-700/35" /></a>
                    @empty
                        <div class="py-8 text-center"><p class="text-sm font-medium">Nothing urgent.</p><p class="mt-1 text-xs text-ink-700/50">Your reminders will appear here.</p></div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="mt-10">
            <div class="flex items-center justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.13em] text-moss-600">My garage</p><h2 class="mt-1 text-2xl font-semibold tracking-tight">All vehicles</h2></div><a href="{{ route('vehicles.index') }}" class="text-sm font-semibold text-moss-600">View garage</a></div>
            <div class="mt-5 grid gap-5 md:grid-cols-2 2xl:grid-cols-3">@foreach($vehicles->take(3) as $vehicle)<x-vehicle-card :vehicle="$vehicle" />@endforeach</div>
        </section>

        <div class="mt-10 grid gap-5 xl:grid-cols-2">
            <section class="rounded-card border border-ink-950/8 bg-paper p-5 shadow-card sm:p-6">
                <div class="flex items-center justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.13em] text-moss-600">In the diary</p><h2 class="mt-1 text-xl font-semibold">Upcoming events</h2></div><a href="{{ route('events.index') }}" class="text-xs font-semibold text-moss-600">Explore all</a></div>
                <div class="mt-5 flex flex-col gap-3">@foreach($events as $event)<a href="{{ route('events.show', $event) }}" class="flex items-center gap-4 rounded-2xl bg-canvas p-3.5 transition hover:bg-sand-100"><span class="grid size-12 shrink-0 place-items-center rounded-xl bg-ink-950 text-white"><span class="text-sm font-semibold">{{ $event->starts_at->format('d') }}</span><span class="-mt-1 text-[0.55rem] uppercase text-white/50">{{ $event->starts_at->format('M') }}</span></span><span class="min-w-0 flex-1"><span class="block truncate text-sm font-semibold">{{ $event->title }}</span><span class="mt-1 flex items-center gap-1 truncate text-xs text-ink-700/50"><x-icon name="map-pin" class="size-3.5" /> {{ $event->city }}</span></span><x-icon name="chevron-right" class="size-4 text-ink-700/30" /></a>@endforeach</div>
            </section>
            <section class="rounded-card border border-ink-950/8 bg-paper p-5 shadow-card sm:p-6">
                <div class="flex items-center justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.13em] text-moss-600">Work in progress</p><h2 class="mt-1 text-xl font-semibold">Bookings</h2></div><a href="{{ route('bookings.index') }}" class="text-xs font-semibold text-moss-600">View all</a></div>
                <div class="mt-5 flex flex-col gap-3">@forelse($bookings as $booking)<div class="flex items-center gap-4 rounded-2xl bg-canvas p-3.5"><span class="grid size-11 place-items-center rounded-xl bg-moss-100 text-moss-700"><x-icon name="wrench" class="size-4.5" /></span><span class="min-w-0 flex-1"><span class="block truncate text-sm font-semibold">{{ $booking->service }}</span><span class="mt-1 block truncate text-xs text-ink-700/50">{{ $booking->specialist->name }} · {{ $booking->requested_start_at->format('j M') }}</span></span><span class="rounded-full bg-white px-2.5 py-1 text-[0.65rem] font-semibold text-moss-700">{{ str($booking->status)->headline() }}</span></div>@empty<div class="rounded-2xl bg-canvas p-6 text-center"><p class="text-sm font-medium">No active bookings.</p><a href="{{ route('specialists.index') }}" class="mt-2 inline-flex text-xs font-semibold text-moss-600">Find a specialist</a></div>@endforelse</div>
            </section>
        </div>
    @endif
</div>
@endsection
