<?php

namespace App\Models;

use Database\Factories\MaintenanceRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['vehicle_id', 'created_by', 'type', 'status', 'urgency', 'title', 'description', 'source', 'due_at', 'completed_at', 'mileage', 'cost_pence', 'provider_name', 'verification_status', 'metadata'])]
class MaintenanceRecord extends Model
{
    /** @use HasFactory<MaintenanceRecordFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'due_at' => 'date',
            'completed_at' => 'date',
            'metadata' => 'array',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function motDefects(): HasMany
    {
        return $this->hasMany(MotDefect::class);
    }
}
