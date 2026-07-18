<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'                    => fake()->randomElement(['Starter', 'Pro', 'Enterprise']),
            'monthly_execution_limit' => fake()->numberBetween(100, 10000),
        ];
    }
}
