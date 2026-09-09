<?php

namespace App\Models;

use Database\Factories\SpecialistFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'tagline', 'description', 'categories', 'brands', 'address', 'city', 'postcode', 'latitude', 'longitude', 'rating', 'review_count', 'is_verified', 'is_featured', 'price_level', 'image_url', 'phone', 'email', 'website_url', 'opening_hours'])]
class Specialist extends Model
{
    /** @use HasFactory<SpecialistFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'brands' => 'array',
            'rating' => 'decimal:1',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'opening_hours' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
