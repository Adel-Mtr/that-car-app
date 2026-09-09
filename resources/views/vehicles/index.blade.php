@extends('layouts.app', ['title' => 'My garage', 'header' => 'My garage'])

@section('content')
<div class="mx-auto max-w-[92rem]">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-medium text-moss-600">Your vehicles</p><h1 class="mt-1 text-3xl font-semibold tracking-[-0.04em] sm:text-4xl">A home for every car.</h1><p class="mt-3 max-w-xl text-sm leading-6 text-ink-700/60">Legal dates, maintenance, history and the moments worth keeping.</p></div><a href="{{ route('vehicles.create') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-ink-950 px-5 py-3 text-sm font-semibold text-white"><x-icon name="plus" class="size-4" /> Add vehicle</a></div>
    @if($vehicles->isEmpty())
        <div class="mt-10 rounded-[2rem] border border-dashed border-ink-950/20 bg-paper p-10 text-center"><span class="mx-auto grid size-14 place-items-center rounded-2xl bg-sand-100"><x-icon name="car" /></span><h2 class="mt-5 text-xl font-semibold">No vehicles yet</h2><p class="mt-2 text-sm text-ink-700/60">Add a registration to create your first vehicle plan.</p></div>
    @else
        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">@foreach($vehicles as $vehicle)<x-vehicle-card :vehicle="$vehicle" />@endforeach</div>
    @endif
</div>
@endsection
