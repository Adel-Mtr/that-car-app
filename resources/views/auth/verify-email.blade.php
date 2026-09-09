@extends('layouts.marketing', ['title' => 'Verify email — '.config('app.name')])

@section('content')
<section class="grid min-h-[calc(100vh-4.5rem)] place-items-center bg-canvas px-5 py-12">
    <div class="w-full max-w-md rounded-card border border-ink-950/8 bg-paper p-7 text-center shadow-card sm:p-9">
        <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-lime-400 text-ink-950"><x-icon name="mail" class="size-5" /></span>
        <h1 class="mt-6 text-3xl font-semibold tracking-[-0.04em]">Check your inbox</h1>
        <p class="mt-3 text-sm leading-6 text-ink-700/55">We sent a verification link to <strong class="text-ink-950">{{ auth()->user()->email }}</strong>. Verify it to protect your garage and documents.</p>
        @if(session('success'))<div class="mt-5 rounded-2xl bg-moss-100 px-4 py-3 text-sm text-moss-700">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">@csrf<button class="w-full rounded-full bg-ink-950 px-5 py-3.5 text-sm font-semibold text-white">Send another link</button></form>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button class="text-sm font-semibold text-moss-600">Sign out</button></form>
    </div>
</section>
@endsection
