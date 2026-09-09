<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Specialist;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'specialist_id' => Specialist::factory(),
            'confirmation_code' => (string) Str::uuid(),
            'service' => fake()->randomElement(['Annual service', 'MOT and inspection', 'Wheel alignment', 'Brake inspection']),
            'description' => fake()->optional()->sentence(),
            'requested_start_at' => now()->addDays(fake()->numberBetween(3, 60))->setTime(9, 0),
            'status' => fake()->randomElement(['requested', 'quoted', 'confirmed']),
            'quote_pence' => fake()->optional()->numberBetween(10000, 120000),
            'paid_pence' => 0,
        ];
    }
}
