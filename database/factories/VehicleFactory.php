<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vehicles = [
            ['Volkswagen', 'Golf', 'GTI', 'Petrol'],
            ['BMW', '3 Series', '320d M Sport', 'Diesel'],
            ['Ford', 'Focus', 'ST-Line', 'Petrol'],
            ['Tesla', 'Model 3', 'Long Range', 'Electric'],
            ['Toyota', 'Corolla', 'Design Hybrid', 'Hybrid'],
        ];
        [$make, $model, $variant, $fuelType] = fake()->randomElement($vehicles);

        return [
            'owner_id' => User::factory(),
            'public_id' => (string) Str::uuid(),
            'registration' => Str::upper(fake()->unique()->bothify('??##???')),
            'make' => $make,
            'model' => $model,
            'variant' => $variant,
            'year' => fake()->numberBetween(2015, 2025),
            'colour' => fake()->randomElement(['Deep blue', 'Graphite grey', 'Pearl white', 'Midnight black', 'Racing red']),
            'fuel_type' => $fuelType,
            'transmission' => fake()->randomElement(['Manual', 'Automatic']),
            'engine_size_cc' => $fuelType === 'Electric' ? null : fake()->randomElement([999, 1498, 1798, 1984, 1995]),
            'current_mileage' => fake()->numberBetween(8000, 95000),
            'annual_mileage' => fake()->numberBetween(5000, 16000),
            'mot_status' => 'valid',
            'mot_due_at' => today()->addDays(fake()->numberBetween(20, 320)),
            'tax_status' => 'taxed',
            'tax_due_at' => today()->addDays(fake()->numberBetween(20, 320)),
            'insurance_due_at' => today()->addDays(fake()->numberBetween(20, 320)),
            'health_score' => fake()->numberBetween(72, 100),
            'valuation_pence' => fake()->numberBetween(600000, 4500000),
            'visibility' => 'private',
            'metadata' => ['provider' => 'factory'],
            'last_synced_at' => now(),
        ];
    }

    public function publiclyVisible(): static
    {
        return $this->state(fn (array $attributes): array => ['visibility' => 'public']);
    }

    public function needsAttention(): static
    {
        return $this->state(fn (array $attributes): array => [
            'health_score' => 62,
            'mot_due_at' => today()->addDays(18),
        ]);
    }
}
