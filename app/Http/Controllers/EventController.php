<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'in:meet,show,track,drive,classic'],
            'city' => ['nullable', 'string', 'max:80'],
        ]);

        $events = Event::query()
            ->where('is_published', true)
            ->where('starts_at', '>=', now())
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->when($filters['city'] ?? null, fn ($query, $city) => $query->where('city', 'like', '%'.$city.'%'))
            ->withCount('attendees')
            ->orderByDesc('is_featured')
            ->orderBy('starts_at')
            ->paginate(9)
            ->withQueryString();

        return view('events.index', compact('events', 'filters'));
    }

    public function show(Request $request, Event $event): View
    {
        abort_unless($event->is_published || $event->organizer_id === $request->user()->id, 404);

        $event->loadCount('attendees');
        $attendance = $event->attendees()
            ->where('users.id', $request->user()->id)
            ->first();
        $vehicles = $request->user()->manageableVehiclesQuery()
            ->select(['id', 'make', 'model', 'registration'])
            ->orderBy('make')
            ->get();

        return view('events.show', compact('event', 'attendance', 'vehicles'));
    }
}
