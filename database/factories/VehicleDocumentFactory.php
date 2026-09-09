<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleDocument>
 */
class VehicleDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'uploaded_by' => User::factory(),
            'title' => fake()->randomElement(['Annual service invoice', 'MOT certificate', 'Insurance schedule']),
            'type' => fake()->randomElement(['service_invoice', 'mot', 'insurance']),
            'disk' => 'local',
            'path' => 'vehicle-documents/example.pdf',
            'original_filename' => 'example.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => fake()->numberBetween(10000, 500000),
            'document_date' => today()->subDays(fake()->numberBetween(1, 400)),
            'verification_status' => 'owner_uploaded',
        ];
    }
}
