<?php

namespace Database\Factories;

use App\Models\Specialist;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Specialist>
 */
class SpecialistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company().' Automotive';

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 9999),
            'tagline' => fake()->sentence(8),
            'description' => fake()->paragraphs(2, true),
            'categories' => fake()->randomElements(['servicing', 'tyres', 'bodywork', 'detailing', 'performance', 'electric', 'classic'], 3),
            'brands' => fake()->randomElements(['BMW', 'Volkswagen', 'Ford', 'Toyota', 'Tesla', 'Porsche'], 3),
            'address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['London', 'Birmingham', 'Oxford', 'Manchester', 'Bristol']),
            'postcode' => fake()->postcode(),
            'rating' => fake()->randomFloat(1, 3.8, 5),
            'review_count' => fake()->numberBetween(12, 450),
            'is_verified' => true,
            'is_featured' => false,
            'price_level' => fake()->randomElement(['£', '££', '£££']),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'website_url' => fake()->url(),
            'opening_hours' => ['weekdays' => '08:00–18:00', 'saturday' => '09:00–14:00', 'sunday' => 'Closed'],
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => ['is_featured' => true]);
    }
}
