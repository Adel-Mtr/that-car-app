<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CompletedReminderController extends Controller
{
    public function store(Vehicle $vehicle, Reminder $reminder): RedirectResponse
    {
        abort_unless($reminder->vehicle_id === $vehicle->id, 404);
        Gate::authorize('update', $reminder);
        $reminder->update(['completed_at' => now()]);

        if ($reminder->recurrence !== null) {
            $nextDueAt = match ($reminder->recurrence) {
                'monthly' => $reminder->due_at->copy()->addMonthNoOverflow(),
                'quarterly' => $reminder->due_at->copy()->addMonthsNoOverflow(3),
                'yearly' => $reminder->due_at->copy()->addYearNoOverflow(),
            };

            $vehicle->reminders()->create([
                'user_id' => $reminder->user_id,
                'maintenance_record_id' => $reminder->maintenance_record_id,
                'category' => $reminder->category,
                'title' => $reminder->title,
                'description' => $reminder->description,
                'due_at' => $nextDueAt,
                'lead_days' => $reminder->lead_days,
                'channel' => $reminder->channel,
                'recurrence' => $reminder->recurrence,
            ]);
        }

        return back()->with('success', $reminder->recurrence === null
            ? 'Reminder completed.'
            : 'Reminder completed. The next occurrence has been scheduled.');
    }
}
