<?php

namespace App\Models;

use Database\Factories\MotDefectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['mot_test_id', 'maintenance_record_id', 'text', 'type', 'dangerous', 'resolved_at'])]
class MotDefect extends Model
{
    /** @use HasFactory<MotDefectFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'dangerous' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function motTest(): BelongsTo
    {
        return $this->belongsTo(MotTest::class);
    }

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class);
    }
}
