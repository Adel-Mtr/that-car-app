<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $events = Event::query()
            ->where('is_published', true)
            ->where('starts_at', '>=', now())
            ->orderByDesc('is_featured')
            ->orderBy('starts_at')
            ->limit(3)
            ->get();
        $specialists = Specialist::query()
            ->where('is_verified', true)
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->limit(3)
            ->get();

        return view('home', compact('events', 'specialists'));
    }
}
