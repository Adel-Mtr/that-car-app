<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
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
            'body' => fake()->paragraph(),
            'category' => fake()->randomElement(['update', 'build', 'question', 'drive']),
            'visibility' => 'public',
            'likes_count' => fake()->numberBetween(0, 180),
            'published_at' => now()->subDays(fake()->numberBetween(0, 30)),
        ];
    }
}
