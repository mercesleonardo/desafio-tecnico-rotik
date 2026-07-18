<?php

namespace Database\Factories;

use App\Enums\AgentStatus;
use App\Models\{Agent, Client};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agent>
 */
class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id'   => Client::factory(),
            'name'        => 'Agente ' . fake()->unique()->word(),
            'description' => fake()->optional()->sentence(),
            'status'      => AgentStatus::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AgentStatus::Inactive,
        ]);
    }
}
