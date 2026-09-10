<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#101512">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas antialiased">
    <div data-sidebar-backdrop class="fixed inset-0 z-40 hidden bg-ink-950/60 backdrop-blur-sm lg:hidden"></div>
    <aside data-sidebar class="fixed inset-y-0 left-0 z-50 flex w-[17.5rem] -translate-x-full flex-col bg-ink-950 text-white transition-transform duration-300 lg:translate-x-0">
        <div class="flex h-20 items-center justify-between px-6">
            <a href="{{ route('dashboard') }}"><x-logo inverse /></a>
            <button data-sidebar-close class="grid size-10 place-items-center rounded-xl text-white/60 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Close menu"><x-icon name="x" /></button>
        </div>
        <nav class="flex flex-1 flex-col gap-1 px-4 pt-4" aria-label="Application">
            @php
                $nav = [
                    ['dashboard', 'home', 'Overview'],
                    ['vehicles.*', 'car', 'My garage'],
                    ['specialists.*', 'wrench', 'Specialists'],
                    ['events.*', 'calendar', 'Events'],
                    ['community', 'users', 'Community'],
                    ['bookings.*', 'ticket', 'Bookings'],
                ];
            @endphp
            @foreach($nav as [$pattern, $icon, $label])
                @php
                    $routeName = match ($pattern) {
                        'dashboard' => 'dashboard',
                        'vehicles.*' => 'vehicles.index',
                        'specialists.*' => 'specialists.index',
                        'events.*' => 'events.index',
                        'community' => 'community',
                        'bookings.*' => 'bookings.index',
                    };
                    $active = request()->routeIs($pattern);
                @endphp
                <a href="{{ route($routeName) }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-3 text-sm font-medium transition {{ $active ? 'bg-white text-ink-950 shadow-sm' : 'text-white/62 hover:bg-white/8 hover:text-white' }}">
                    <x-icon :name="$icon" class="size-5" />
                    <span>{{ $label }}</span>
                    @if($active)<span class="ml-auto size-1.5 rounded-full bg-moss-600"></span>@endif
                </a>
            @endforeach
        </nav>
        <div class="flex flex-col gap-3 p-4">
            <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                <div class="flex items-center gap-3">
                    <span class="grid size-10 place-items-center rounded-full bg-lime-400 text-sm font-bold text-ink-950">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-white/45">{{ '@'.auth()->user()->username }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="text-white/45 transition hover:text-white" aria-label="Settings"><x-icon name="settings" class="size-4.5" /></a>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-white/50 transition hover:bg-white/8 hover:text-white"><x-icon name="logout" class="size-4.5" /> Sign out</button>
            </form>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-[17.5rem]">
        <header class="sticky top-0 z-30 flex h-18 items-center border-b border-ink-950/8 bg-canvas/88 px-4 backdrop-blur-xl sm:px-6 lg:px-8">
            <button data-sidebar-open class="mr-3 grid size-10 place-items-center rounded-xl bg-white text-ink-800 shadow-sm lg:hidden" aria-label="Open menu"><x-icon name="menu" /></button>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-ink-950">{{ $header ?? $title ?? config('app.name') }}</p>
                <p class="hidden text-xs text-ink-700/60 sm:block">{{ now()->format('l, j F') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('vehicles.create') }}" class="hidden items-center gap-2 rounded-full bg-ink-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-ink-800 sm:inline-flex"><x-icon name="plus" class="size-4" /> Add car</a>
                <form method="POST" action="{{ route('notifications.read') }}">
                    @csrf
                    <button class="relative grid size-10 place-items-center rounded-full bg-white text-ink-700 shadow-sm transition hover:text-ink-950" aria-label="Mark notifications as read">
                        <x-icon name="bell" class="size-4.5" />
                        @if(($unreadNotificationCount ?? 0) > 0)
                            <span class="absolute right-1 top-1 size-2 rounded-full bg-rose-700 ring-2 ring-white"></span>
                        @endif
                    </button>
                </form>
            </div>
        </header>

        <x-demo-notice />

        @if(session('success'))
            <div data-dismissible class="mx-4 mt-4 flex items-center gap-3 rounded-2xl border border-moss-600/15 bg-moss-100 px-4 py-3 text-sm text-moss-700 shadow-sm sm:mx-6 lg:mx-8">
                <span class="grid size-7 place-items-center rounded-full bg-moss-600 text-white"><x-icon name="check" class="size-4" /></span>
                <p class="flex-1 font-medium">{{ session('success') }}</p>
                <button data-dismiss class="text-moss-700/60 hover:text-moss-700" aria-label="Dismiss"><x-icon name="x" class="size-4" /></button>
            </div>
        @endif

        @if($errors->any())
            <div data-dismissible class="mx-4 mt-4 flex items-start gap-3 rounded-2xl border border-rose-700/15 bg-rose-100 px-4 py-3 text-sm text-rose-700 shadow-sm sm:mx-6 lg:mx-8">
                <span class="mt-0.5 grid size-7 place-items-center rounded-full bg-rose-700 text-white">!</span>
                <div class="flex-1"><p class="font-semibold">Please check the highlighted details.</p><p class="mt-0.5 text-rose-700/75">{{ $errors->first() }}</p></div>
                <button data-dismiss class="text-rose-700/60 hover:text-rose-700" aria-label="Dismiss"><x-icon name="x" class="size-4" /></button>
            </div>
        @endif

        <main class="px-4 pb-28 pt-6 sm:px-6 lg:px-8 lg:pb-10">@yield('content')</main>
    </div>

    <nav class="fixed inset-x-3 bottom-3 z-30 flex h-17 items-center justify-around rounded-[1.35rem] border border-white/70 bg-ink-950/95 px-2 text-white shadow-lift backdrop-blur-xl lg:hidden" aria-label="Mobile navigation">
        @foreach([
            ['dashboard', 'dashboard', 'home', 'Home'],
            ['vehicles.index', 'vehicles.*', 'car', 'Garage'],
            ['events.index', 'events.*', 'calendar', 'Events'],
            ['community', 'community', 'users', 'Social'],
        ] as [$routeName, $pattern, $icon, $label])
            <a href="{{ route($routeName) }}" class="flex min-w-16 flex-col items-center gap-1 rounded-xl py-2 text-[0.67rem] font-medium {{ request()->routeIs($pattern) ? 'text-lime-400' : 'text-white/55' }}"><x-icon :name="$icon" class="size-5" /><span>{{ $label }}</span></a>
        @endforeach
    </nav>
</body>
</html>
