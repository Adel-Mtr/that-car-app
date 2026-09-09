<?php

namespace Database\Factories;

use App\Models\MotTest;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MotTest>
 */
class MotTestFactory extends Factory
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
            'test_number' => fake()->unique()->numerify('##########'),
            'completed_at' => now()->subYear(),
            'expiry_at' => today()->addDays(fake()->numberBetween(20, 300)),
            'result' => 'passed',
            'odometer_value' => fake()->numberBetween(10000, 100000),
            'odometer_unit' => 'mi',
            'data_source' => 'dvsa',
        ];
    }
}
