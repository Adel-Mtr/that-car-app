<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'bio', 'avatar_url', 'postcode', 'onboarding_completed', 'preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmail, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
            'preferences' => 'array',
        ];
    }

    public function ownedVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'owner_id');
    }

    public function sharedVehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_user')
            ->withPivot(['role', 'accepted_at'])
            ->withTimestamps();
    }

    public function accessibleVehiclesQuery(): Builder
    {
        return Vehicle::query()->where(function (Builder $query): void {
            $query->where('owner_id', $this->id)
                ->orWhereHas('members', fn (Builder $memberQuery) => $memberQuery
                    ->where('users.id', $this->id)
                    ->whereNotNull('vehicle_user.accepted_at'));
        });
    }

    public function manageableVehiclesQuery(): Builder
    {
        return Vehicle::query()->where(function (Builder $query): void {
            $query->where('owner_id', $this->id)
                ->orWhereHas('members', fn (Builder $memberQuery) => $memberQuery
                    ->where('users.id', $this->id)
                    ->where('vehicle_user.role', 'manager')
                    ->whereNotNull('vehicle_user.accepted_at'));
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function organizedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot(['vehicle_id', 'status'])
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }
}
