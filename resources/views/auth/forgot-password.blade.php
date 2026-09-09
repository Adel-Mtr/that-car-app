@extends('layouts.marketing', ['title' => 'Reset password — '.config('app.name')])

@section('content')
<section class="grid min-h-[calc(100vh-4.5rem)] place-items-center bg-canvas px-5 py-12">
    <div class="w-full max-w-md rounded-card border border-ink-950/8 bg-paper p-7 shadow-card sm:p-9">
        <span class="grid size-11 place-items-center rounded-2xl bg-moss-100 text-moss-700"><x-icon name="key" class="size-5" /></span>
        <h1 class="mt-6 text-3xl font-semibold tracking-[-0.04em]">Reset your password</h1>
        <p class="mt-2 text-sm leading-6 text-ink-700/55">Enter the email on your account and we’ll send a secure reset link.</p>
        @if(session('success'))<div class="mt-5 rounded-2xl bg-moss-100 px-4 py-3 text-sm text-moss-700">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('password.email') }}" class="mt-6 flex flex-col gap-4">@csrf
            <label class="flex flex-col gap-2 text-sm font-medium">Email address<input name="email" type="email" autocomplete="email" required autofocus value="{{ old('email') }}" class="rounded-2xl border border-ink-950/12 bg-canvas px-4 py-3.5 outline-none transition focus:border-moss-600"></label>
            @error('email')<p class="text-xs font-medium text-rose-700">{{ $message }}</p>@enderror
            <button class="rounded-full bg-ink-950 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-moss-700">Email reset link</button>
        </form>
        <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-semibold text-moss-600">Back to sign in</a>
    </div>
</section>
@endsection
