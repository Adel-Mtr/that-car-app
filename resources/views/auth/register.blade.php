@extends('layouts.marketing', ['title' => 'Create your garage — '.config('app.name')])

@section('content')
<section class="min-h-[calc(100vh-9rem)] bg-canvas px-5 py-14">
    <div class="mx-auto grid max-w-5xl overflow-hidden rounded-[2rem] border border-ink-950/8 bg-white shadow-card lg:grid-cols-[.9fr_1.1fr]">
        <div class="relative hidden overflow-hidden bg-moss-700 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -left-20 bottom-12 size-64 rounded-full bg-lime-400/20 blur-3xl"></div>
            <div class="relative"><span class="inline-flex rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-lime-300">Takes about a minute</span><h1 class="mt-6 text-4xl font-semibold leading-tight tracking-[-0.045em]">A calmer way to own the car you rely on.</h1></div>
            <ul class="relative flex flex-col gap-4 text-sm text-white/70">
                <li class="flex gap-3"><x-icon name="check" class="mt-0.5 size-4 text-lime-300" /> Clear maintenance priorities</li>
                <li class="flex gap-3"><x-icon name="check" class="mt-0.5 size-4 text-lime-300" /> One continuous vehicle history</li>
                <li class="flex gap-3"><x-icon name="check" class="mt-0.5 size-4 text-lime-300" /> Private by default</li>
            </ul>
        </div>
        <div class="p-7 sm:p-10 lg:p-12">
            <p class="text-sm font-semibold text-moss-600">Create your account</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-[-0.04em]">Start your garage.</h2>
            <form method="POST" action="{{ route('register.store') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
                @csrf
                <label class="flex flex-col gap-2 text-sm font-medium text-ink-800 sm:col-span-2">Your name<input name="name" value="{{ old('name') }}" required autocomplete="name" class="rounded-2xl border border-ink-950/12 bg-paper px-4 py-3.5 text-sm outline-none focus:border-moss-600 focus:ring-4 focus:ring-moss-100"></label>
                <label class="flex flex-col gap-2 text-sm font-medium text-ink-800">Username<input name="username" value="{{ old('username') }}" required autocomplete="username" placeholder="alexdrives" class="rounded-2xl border border-ink-950/12 bg-paper px-4 py-3.5 text-sm outline-none focus:border-moss-600 focus:ring-4 focus:ring-moss-100"></label>
                <label class="flex flex-col gap-2 text-sm font-medium text-ink-800">Postcode <span class="font-normal text-ink-700/45">Optional</span><input name="postcode" value="{{ old('postcode') }}" autocomplete="postal-code" class="rounded-2xl border border-ink-950/12 bg-paper px-4 py-3.5 text-sm outline-none focus:border-moss-600 focus:ring-4 focus:ring-moss-100"></label>
                <label class="flex flex-col gap-2 text-sm font-medium text-ink-800 sm:col-span-2">Email address<input name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="rounded-2xl border border-ink-950/12 bg-paper px-4 py-3.5 text-sm outline-none focus:border-moss-600 focus:ring-4 focus:ring-moss-100"></label>
                <label class="flex flex-col gap-2 text-sm font-medium text-ink-800">Password<input name="password" type="password" required autocomplete="new-password" class="rounded-2xl border border-ink-950/12 bg-paper px-4 py-3.5 text-sm outline-none focus:border-moss-600 focus:ring-4 focus:ring-moss-100"></label>
                <label class="flex flex-col gap-2 text-sm font-medium text-ink-800">Confirm password<input name="password_confirmation" type="password" required autocomplete="new-password" class="rounded-2xl border border-ink-950/12 bg-paper px-4 py-3.5 text-sm outline-none focus:border-moss-600 focus:ring-4 focus:ring-moss-100"></label>
                <button class="mt-2 inline-flex items-center justify-center gap-2 rounded-full bg-ink-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-ink-800 sm:col-span-2">Create my garage <x-icon name="arrow-right" class="size-4" /></button>
            </form>
            <p class="mt-6 text-sm text-ink-700/70">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-moss-600">Sign in</a></p>
        </div>
    </div>
</section>
@endsection
