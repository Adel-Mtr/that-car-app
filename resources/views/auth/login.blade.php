@extends('layouts.marketing', ['title' => 'Sign in — '.config('app.name')])

@section('content')
<section class="min-h-[calc(100vh-9rem)] bg-canvas px-5 py-14">
    <div class="mx-auto grid max-w-5xl overflow-hidden rounded-[2rem] border border-ink-950/8 bg-white shadow-card lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-ink-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-20 top-10 size-64 rounded-full bg-lime-400/15 blur-3xl"></div>
            <div class="relative"><span class="inline-flex rounded-full bg-white/8 px-3 py-1.5 text-xs font-semibold text-lime-300">Welcome back</span><h1 class="mt-6 text-4xl font-semibold leading-tight tracking-[-0.045em]">Your cars have been keeping the record warm.</h1></div>
            <div class="relative rounded-2xl border border-white/10 bg-white/6 p-5"><p class="text-sm leading-6 text-white/65">Demo access</p><p class="mt-2 font-mono text-xs text-lime-300">demo@thatcarapp.test / password</p></div>
        </div>
        <div class="p-7 sm:p-10 lg:p-12">
            <div class="max-w-sm">
                <p class="text-sm font-semibold text-moss-600">Sign in</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-[-0.04em]">Back to your garage.</h2>
                <p class="mt-3 text-sm leading-6 text-ink-700/65">Review upcoming work, keep records current and find your next drive.</p>
            </div>
            <form method="POST" action="{{ route('login.store') }}" class="mt-8 flex flex-col gap-5">
                @csrf
                <label class="flex flex-col gap-2 text-sm font-medium text-ink-800">Email address<input name="email" type="email" value="{{ old('email', 'demo@thatcarapp.test') }}" required autocomplete="email" class="rounded-2xl border border-ink-950/12 bg-paper px-4 py-3.5 text-sm outline-none transition focus:border-moss-600 focus:ring-4 focus:ring-moss-100"></label>
                <label class="flex flex-col gap-2 text-sm font-medium text-ink-800">Password<input name="password" type="password" value="password" required autocomplete="current-password" class="rounded-2xl border border-ink-950/12 bg-paper px-4 py-3.5 text-sm outline-none transition focus:border-moss-600 focus:ring-4 focus:ring-moss-100"></label>
                <div class="-mt-2 text-right"><a href="{{ route('password.request') }}" class="text-xs font-semibold text-moss-600 hover:text-moss-700">Forgot your password?</a></div>
                <label class="flex items-center gap-2.5 text-sm text-ink-700"><input type="checkbox" name="remember" value="1" class="size-4 rounded border-ink-950/20 text-moss-600"> Keep me signed in</label>
                <button class="mt-1 inline-flex items-center justify-center gap-2 rounded-full bg-ink-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-ink-800">Sign in <x-icon name="arrow-right" class="size-4" /></button>
            </form>
            <p class="mt-7 text-sm text-ink-700/70">New to {{ config('app.name') }}? <a href="{{ route('register') }}" class="font-semibold text-moss-600 hover:text-moss-700">Create your garage</a></p>
        </div>
    </div>
</section>
@endsection
