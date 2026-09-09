<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101512">
    <meta name="description" content="{{ config('app.name') }} turns vehicle data into a clear maintenance plan, trusted history and a community for people who care about cars.">
    <title>{{ $title ?? config('app.name').' — know what your car needs next' }}</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper antialiased">
    <header class="relative z-20 border-b border-ink-950/8 bg-paper/90 backdrop-blur-xl">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between px-5 lg:px-8">
            <a href="{{ route('home') }}" aria-label="{{ config('app.name') }} home"><x-logo /></a>
            <nav class="hidden items-center gap-8 text-sm font-medium text-ink-700 md:flex" aria-label="Primary">
                <a href="{{ route('home') }}#how-it-works" class="transition hover:text-ink-950">How it works</a>
                <a href="{{ route('home') }}#features" class="transition hover:text-ink-950">Features</a>
                <a href="{{ route('home') }}#community" class="transition hover:text-ink-950">Community</a>
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-ink-950 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ink-800">Open app</a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-full px-4 py-2.5 text-sm font-semibold text-ink-800 transition hover:bg-sand-100 sm:block">Sign in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-ink-950 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ink-800">Get started</a>
                @endauth
            </div>
        </div>
    </header>

    <main>{{ $slot ?? '' }}@yield('content')</main>

    <footer class="border-t border-ink-950/10 bg-ink-950 text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-8 px-5 py-10 sm:flex-row sm:items-end sm:justify-between lg:px-8">
            <div class="flex flex-col gap-3">
                <x-logo inverse />
                <p class="max-w-sm text-sm leading-6 text-white/55">Your car’s health, history and community — clear enough for every driver, detailed enough for enthusiasts.</p>
            </div>
            <p class="text-xs text-white/40">© {{ now()->year }} {{ config('app.name') }}. Private by design.</p>
        </div>
    </footer>
</body>
</html>
