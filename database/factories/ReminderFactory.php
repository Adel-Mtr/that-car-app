<?php

namespace Database\Factories;

use App\Models\Reminder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
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
            'user_id' => User::factory(),
            'category' => fake()->randomElement(['mot', 'tax', 'insurance', 'service']),
            'title' => fake()->randomElement(['MOT renewal', 'Vehicle tax renewal', 'Insurance renewal', 'Annual service']),
            'description' => fake()->optional()->sentence(),
            'due_at' => now()->addDays(fake()->numberBetween(5, 180)),
            'lead_days' => fake()->randomElement([7, 14, 30]),
            'channel' => 'database',
        ];
    }
}
