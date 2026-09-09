<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->withCount('attendees')
            ->orderByDesc('starts_at')
            ->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.events.create', ['event' => new Event]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['organizer_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['title']);

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validated($request, $event);
        if ($event->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $event);
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Event $event = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:320'],
            'description' => ['required', 'string', 'max:10000'],
            'category' => ['required', Rule::in(['meet', 'show', 'track', 'drive', 'classic'])],
            'venue' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'postcode' => ['nullable', 'string', 'max:16'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'price_pounds' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'cover_image_url' => ['nullable', 'url', 'max:2048'],
            'vehicle_tags' => ['nullable', 'string', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['price_pence'] = (int) round(((float) ($data['price_pounds'] ?? 0)) * 100);
        unset($data['price_pounds']);
        $data['vehicle_tags'] = $this->commaSeparated($data['vehicle_tags'] ?? null);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }

    private function uniqueSlug(string $title, ?Event $ignore = null): string
    {
        $base = Str::slug($title) ?: 'event';
        $slug = $base;
        $suffix = 2;

        while (Event::query()->where('slug', $slug)->when($ignore, fn ($query) => $query->whereKeyNot($ignore->getKey()))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    /** @return list<string>|null */
    private function commaSeparated(?string $value): ?array
    {
        $items = collect(explode(',', (string) $value))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $items === [] ? null : $items;
    }
}
