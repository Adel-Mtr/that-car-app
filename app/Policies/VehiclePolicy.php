<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        return $vehicle->owner_id === $user->id
            || $vehicle->members()
                ->where('users.id', $user->id)
                ->whereNotNull('vehicle_user.accepted_at')
                ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $vehicle->owner_id === $user->id
            || $vehicle->members()
                ->where('users.id', $user->id)
                ->wherePivotIn('role', ['owner', 'manager'])
                ->whereNotNull('vehicle_user.accepted_at')
                ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $vehicle->owner_id === $user->id;
    }

    public function manageMembers(User $user, Vehicle $vehicle): bool
    {
        return $vehicle->owner_id === $user->id;
    }
}
