<?php

namespace App\Policies;

use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Models\Vehicle;

class MaintenanceRecordPolicy
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
    public function view(User $user, MaintenanceRecord $maintenanceRecord): bool
    {
        return $user->can('view', $maintenanceRecord->vehicle);
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
    public function update(User $user, MaintenanceRecord $maintenanceRecord): bool
    {
        return $user->can('update', $maintenanceRecord->vehicle);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MaintenanceRecord $maintenanceRecord): bool
    {
        return $user->can('update', $maintenanceRecord->vehicle);
    }
}
