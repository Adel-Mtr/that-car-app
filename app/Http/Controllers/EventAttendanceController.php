<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class EventAttendanceController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        abort_unless($event->is_published, 404);
        $attributes = $request->validate([
            'status' => ['required', Rule::in(['going', 'interested'])],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
        ]);

        if ($attributes['vehicle_id'] ?? null) {
            $vehicle = Vehicle::findOrFail($attributes['vehicle_id']);
            Gate::authorize('update', $vehicle);
        }

        $event->attendees()->syncWithoutDetaching([
            $request->user()->id => [
                'status' => $attributes['status'],
                'vehicle_id' => $attributes['vehicle_id'] ?? null,
            ],
        ]);

        return back()->with('success', $attributes['status'] === 'going' ? 'You are going.' : 'Saved to your events.');
    }

    public function destroy(Request $request, Event $event): RedirectResponse
    {
        $event->attendees()->detach($request->user());

        return back()->with('success', 'Removed from your events.');
    }
}
