<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $bookings = Booking::query()
            ->with(['user:id,name,email', 'vehicle:id,make,model,registration', 'specialist:id,name'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByRaw("CASE WHEN status = 'requested' THEN 0 ELSE 1 END")
            ->orderBy('requested_start_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'status'));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['requested', 'quoted', 'confirmed', 'completed', 'cancelled'])],
            'quote_pounds' => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        $booking->status = $data['status'];
        $booking->quote_pence = filled($data['quote_pounds'] ?? null)
            ? (int) round(((float) $data['quote_pounds']) * 100)
            : null;
        $booking->completed_at = $data['status'] === 'completed' ? ($booking->completed_at ?? now()) : null;
        $booking->save();

        return back()->with('success', 'Booking updated.');
    }
}
