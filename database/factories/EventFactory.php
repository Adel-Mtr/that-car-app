<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->randomElement(['Sunday Cars & Coffee', 'Modern Classics Gathering', 'Bicester Heritage Drive', 'Open Pitlane Evening']);

        return [
            'organizer_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 9999),
            'summary' => fake()->sentence(16),
            'description' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(['meet', 'show', 'track', 'drive', 'classic']),
            'venue' => fake()->company(),
            'city' => fake()->randomElement(['London', 'Birmingham', 'Oxford', 'Manchester', 'Bristol']),
            'postcode' => fake()->postcode(),
            'starts_at' => now()->addDays(fake()->numberBetween(7, 180))->setTime(fake()->numberBetween(8, 18), 0),
            'ends_at' => now()->addDays(fake()->numberBetween(181, 250)),
            'capacity' => fake()->optional()->numberBetween(40, 400),
            'price_pence' => fake()->randomElement([0, 0, 0, 1000, 2500]),
            'vehicle_tags' => fake()->randomElements(['Classic', 'Performance', 'JDM', 'German', 'All cars'], 2),
            'is_featured' => false,
            'is_published' => true,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => ['is_featured' => true]);
    }
}
