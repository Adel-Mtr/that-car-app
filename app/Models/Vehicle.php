<?php

namespace App\Models;

use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['owner_id', 'public_id', 'registration', 'vin', 'make', 'model', 'variant', 'year', 'colour', 'fuel_type', 'transmission', 'engine_size_cc', 'current_mileage', 'annual_mileage', 'mot_status', 'mot_due_at', 'tax_status', 'tax_due_at', 'insurance_due_at', 'health_score', 'valuation_pence', 'image_path', 'visibility', 'metadata', 'last_synced_at'])]
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'mot_due_at' => 'date',
            'tax_due_at' => 'date',
            'insurance_due_at' => 'date',
            'metadata' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'vehicle_user')
            ->withPivot(['role', 'accepted_at'])
            ->withTimestamps();
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function vehicleDocuments(): HasMany
    {
        return $this->documents();
    }

    public function motTests(): HasMany
    {
        return $this->hasMany(MotTest::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function displayName(): string
    {
        return trim($this->year.' '.$this->make.' '.$this->model);
    }
}
