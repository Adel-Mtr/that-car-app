<?php

namespace App\Policies;

use App\Models\Reminder;
use App\Models\User;
use App\Models\Vehicle;

class ReminderPolicy
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
    public function view(User $user, Reminder $reminder): bool
    {
        return $reminder->user_id === $user->id;
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
    public function update(User $user, Reminder $reminder): bool
    {
        return $reminder->user_id === $user->id && $user->can('update', $reminder->vehicle);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reminder $reminder): bool
    {
        return $reminder->user_id === $user->id && $user->can('update', $reminder->vehicle);
    }
}
