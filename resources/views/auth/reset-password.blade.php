@extends('layouts.marketing', ['title' => 'Choose a new password — '.config('app.name')])

@section('content')
<section class="grid min-h-[calc(100vh-4.5rem)] place-items-center bg-canvas px-5 py-12">
    <div class="w-full max-w-md rounded-card border border-ink-950/8 bg-paper p-7 shadow-card sm:p-9">
        <h1 class="text-3xl font-semibold tracking-[-0.04em]">Choose a new password</h1>
        <p class="mt-2 text-sm leading-6 text-ink-700/55">Use at least eight characters and something you do not use elsewhere.</p>
        <form method="POST" action="{{ route('password.store') }}" class="mt-6 flex flex-col gap-4">@csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label class="flex flex-col gap-2 text-sm font-medium">Email address<input name="email" type="email" autocomplete="email" required value="{{ old('email', $email) }}" class="rounded-2xl border border-ink-950/12 bg-canvas px-4 py-3.5"></label>
            <label class="flex flex-col gap-2 text-sm font-medium">New password<input name="password" type="password" autocomplete="new-password" required class="rounded-2xl border border-ink-950/12 bg-canvas px-4 py-3.5"></label>
            <label class="flex flex-col gap-2 text-sm font-medium">Confirm password<input name="password_confirmation" type="password" autocomplete="new-password" required class="rounded-2xl border border-ink-950/12 bg-canvas px-4 py-3.5"></label>
            @if($errors->any())<p class="text-xs font-medium text-rose-700">{{ $errors->first() }}</p>@endif
            <button class="rounded-full bg-ink-950 px-5 py-3.5 text-sm font-semibold text-white">Save new password</button>
        </form>
    </div>
</section>
@endsection
