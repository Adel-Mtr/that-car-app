<?php

namespace Database\Factories;

use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceRecord>
 */
class MaintenanceRecordFactory extends Factory
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
            'created_by' => User::factory(),
            'type' => fake()->randomElement(['service', 'repair', 'inspection', 'tyres']),
            'status' => 'planned',
            'urgency' => fake()->randomElement(['routine', 'attention']),
            'title' => fake()->randomElement(['Annual service', 'Front tyre inspection', 'Brake fluid replacement', 'Air conditioning check']),
            'description' => fake()->sentence(),
            'source' => 'owner',
            'due_at' => today()->addDays(fake()->numberBetween(7, 150)),
            'mileage' => fake()->numberBetween(10000, 90000),
            'verification_status' => 'owner_entered',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'completed',
            'due_at' => null,
            'completed_at' => today()->subDays(fake()->numberBetween(1, 500)),
            'cost_pence' => fake()->numberBetween(8500, 95000),
            'provider_name' => fake()->company(),
            'verification_status' => 'document_supported',
        ]);
    }
}
