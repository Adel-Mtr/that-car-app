<?php

namespace App\Models;

use Database\Factories\MotTestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['vehicle_id', 'test_number', 'completed_at', 'expiry_at', 'result', 'odometer_value', 'odometer_unit', 'data_source'])]
class MotTest extends Model
{
    /** @use HasFactory<MotTestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'expiry_at' => 'date',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function defects(): HasMany
    {
        return $this->hasMany(MotDefect::class);
    }
}
