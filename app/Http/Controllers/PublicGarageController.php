<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\View\View;

class PublicGarageController extends Controller
{
    public function show(string $publicId): View
    {
        $vehicle = Vehicle::query()
            ->where('public_id', $publicId)
            ->where('visibility', 'public')
            ->with([
                'owner:id,name,username,avatar_url,bio',
                'maintenanceRecords' => fn ($query) => $query
                    ->where('status', 'completed')
                    ->latest('completed_at')
                    ->limit(12),
                'posts' => fn ($query) => $query
                    ->where('visibility', 'public')
                    ->latest('published_at')
                    ->limit(6),
            ])
            ->firstOrFail();

        return view('garage.public', compact('vehicle'));
    }
}
