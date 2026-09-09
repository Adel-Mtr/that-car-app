<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Specialist;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->whereBelongsTo($request->user())
            ->with(['vehicle:id,make,model,registration', 'specialist:id,name,slug,city'])
            ->orderByRaw("CASE WHEN status IN ('requested', 'quoted', 'confirmed') THEN 0 ELSE 1 END")
            ->orderBy('requested_start_at')
            ->paginate(12);

        return view('bookings.index', compact('bookings'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $vehicle = Vehicle::findOrFail($request->integer('vehicle_id'));
        Gate::authorize('update', $vehicle);
        $specialist = Specialist::findOrFail($request->integer('specialist_id'));

        $booking = Booking::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'confirmation_code' => (string) Str::uuid(),
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Request sent to '.$specialist->name.'. Reference '.Str::upper(Str::substr($booking->confirmation_code, 0, 8)).'.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        Gate::authorize('delete', $booking);
        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking request cancelled.');
    }
}
