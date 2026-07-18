<?php

namespace Database\Factories;

use App\Models\{Client, Plan};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'    => fake()->company(),
            'plan_id' => Plan::factory(),
        ];
    }
}
