<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleMemberRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\VehicleAccessGrantedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class VehicleMemberController extends Controller
{
    public function store(StoreVehicleMemberRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $attributes = $request->validated();
        $member = User::query()->where('email', $attributes['email'])->firstOrFail();

        $vehicle->members()->syncWithoutDetaching([
            $member->id => [
                'role' => $attributes['role'],
                'accepted_at' => now(),
            ],
        ]);

        $member->notify((new VehicleAccessGrantedNotification($vehicle, $request->user()))->afterCommit());

        return back()->with('success', $member->name.' can now access this vehicle.');
    }

    public function destroy(Vehicle $vehicle, User $member): RedirectResponse
    {
        Gate::authorize('manageMembers', $vehicle);
        abort_unless($vehicle->members()->where('users.id', $member->id)->exists(), 404);

        $vehicle->members()->detach($member);

        return back()->with('success', $member->name.' no longer has access.');
    }
}
