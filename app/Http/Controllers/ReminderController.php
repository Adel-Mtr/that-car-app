<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Models\Reminder;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ReminderController extends Controller
{
    public function store(StoreReminderRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->reminders()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'channel' => 'database',
        ]);

        return back()->with('success', 'Reminder scheduled.');
    }

    public function destroy(Vehicle $vehicle, Reminder $reminder): RedirectResponse
    {
        abort_unless($reminder->vehicle_id === $vehicle->id, 404);
        Gate::authorize('delete', $reminder);
        $reminder->delete();

        return back()->with('success', 'Reminder removed.');
    }
}
