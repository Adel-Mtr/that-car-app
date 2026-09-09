<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleDocument;

class VehicleDocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehicleDocument $vehicleDocument): bool
    {
        return $user->can('view', $vehicleDocument->vehicle);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Vehicle $vehicle): bool
    {
        return $user->can('update', $vehicle);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehicleDocument $vehicleDocument): bool
    {
        return $user->can('update', $vehicleDocument->vehicle);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehicleDocument $vehicleDocument): bool
    {
        return $user->can('update', $vehicleDocument->vehicle);
    }
}
