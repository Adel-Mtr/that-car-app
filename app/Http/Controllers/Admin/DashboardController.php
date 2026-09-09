<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Post;
use App\Models\Specialist;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'members' => User::count(),
            'vehicles' => Vehicle::count(),
            'bookings' => Booking::count(),
            'events' => Event::count(),
        ];
        $recentUsers = User::query()->latest()->limit(6)->get();
        $pendingBookings = Booking::query()
            ->where('status', 'requested')
            ->with(['user:id,name,email', 'vehicle:id,make,model,registration', 'specialist:id,name'])
            ->oldest()
            ->limit(8)
            ->get();
        $contentCounts = [
            'posts' => Post::count(),
            'published_events' => Event::where('is_published', true)->count(),
            'verified_specialists' => Specialist::where('is_verified', true)->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentUsers', 'pendingBookings', 'contentCounts'));
    }
}
