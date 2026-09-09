<?php

namespace App\Models;

use Database\Factories\VehicleDocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['vehicle_id', 'uploaded_by', 'title', 'type', 'disk', 'path', 'original_filename', 'mime_type', 'size_bytes', 'document_date', 'verification_status', 'verified_at', 'extracted_data'])]
class VehicleDocument extends Model
{
    /** @use HasFactory<VehicleDocumentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'verified_at' => 'datetime',
            'extracted_data' => 'array',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
