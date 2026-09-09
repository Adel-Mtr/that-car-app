<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $vehicles = $user->accessibleVehiclesQuery()
            ->withCount(['maintenanceRecords as open_items_count' => fn ($query) => $query->where('status', 'planned')])
            ->orderBy('health_score')
            ->orderBy('id')
            ->get();

        $reminders = Reminder::query()
            ->whereBelongsTo($user)
            ->whereNull('completed_at')
            ->with('vehicle:id,make,model,registration')
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        $events = Event::query()
            ->where('is_published', true)
            ->where('starts_at', '>=', now())
            ->withCount('attendees')
            ->orderByDesc('is_featured')
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        $bookings = Booking::query()
            ->whereBelongsTo($user)
            ->whereIn('status', ['requested', 'quoted', 'confirmed'])
            ->with(['vehicle:id,make,model,registration', 'specialist:id,name,slug'])
            ->orderBy('requested_start_at')
            ->limit(3)
            ->get();

        return view('dashboard', compact('vehicles', 'reminders', 'events', 'bookings'));
    }
}
