<?php

namespace Database\Factories;

use App\Models\MotDefect;
use App\Models\MotTest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MotDefect>
 */
class MotDefectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mot_test_id' => MotTest::factory(),
            'text' => 'Front tyre worn close to the legal limit',
            'type' => 'advisory',
            'dangerous' => false,
        ];
    }
}
