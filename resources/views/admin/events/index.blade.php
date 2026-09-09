@extends('layouts.app', ['title' => 'Manage events', 'header' => 'Administration · Events'])

@section('content')
<div class="mx-auto max-w-[92rem]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-ink-700/55 hover:text-ink-950">← Administration</a><h1 class="mt-2 text-3xl font-semibold tracking-[-0.04em]">Events</h1><p class="mt-2 max-w-2xl text-sm text-ink-700/55">Publish, feature and maintain the event directory.</p></div>
        <a href="{{ route('admin.events.create') }}" class="inline-flex items-center justify-center rounded-full bg-ink-950 px-5 py-3 text-sm font-semibold text-white">Create event</a>
    </div>
    <div class="mt-7 overflow-hidden rounded-card border border-ink-950/8 bg-paper shadow-card">
        <div class="overflow-x-auto"><table class="w-full min-w-[780px] text-left"><thead class="bg-canvas text-[0.65rem] uppercase tracking-wider text-ink-700/45"><tr><th class="px-5 py-3 font-semibold">Event</th><th class="px-5 py-3 font-semibold">When</th><th class="px-5 py-3 font-semibold">City</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Attendance</th><th class="px-5 py-3"></th></tr></thead><tbody class="divide-y divide-ink-950/8">
            @forelse($events as $event)
                <tr class="text-sm"><td class="px-5 py-4"><p class="font-semibold">{{ $event->title }}</p><p class="mt-1 text-xs text-ink-700/45">{{ ucfirst($event->category) }}</p></td><td class="px-5 py-4 text-ink-700/65">{{ $event->starts_at->format('j M Y, H:i') }}</td><td class="px-5 py-4">{{ $event->city }}</td><td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $event->is_published ? 'bg-moss-100 text-moss-700' : 'bg-canvas text-ink-700/55' }}">{{ $event->is_published ? 'Published' : 'Draft' }}</span>@if($event->is_featured)<span class="ml-1 rounded-full bg-[#f2d749]/35 px-2.5 py-1 text-xs font-semibold">Featured</span>@endif</td><td class="px-5 py-4">{{ number_format($event->attendees_count) }}</td><td class="px-5 py-4"><div class="flex justify-end gap-2"><a href="{{ route('admin.events.edit', $event) }}" class="rounded-full border border-ink-950/10 px-3 py-2 text-xs font-semibold">Edit</a><form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?')">@csrf @method('DELETE')<button class="rounded-full border border-rose-700/15 px-3 py-2 text-xs font-semibold text-rose-700">Delete</button></form></div></td></tr>
            @empty<tr><td colspan="6" class="px-5 py-12 text-center text-sm text-ink-700/45">No events yet.</td></tr>@endforelse
        </tbody></table></div>
    </div>
    <div class="mt-5">{{ $events->links() }}</div>
</div>
@endsection
