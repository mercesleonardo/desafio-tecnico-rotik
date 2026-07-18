<?php

namespace Database\Factories;

use App\Enums\Enums\ExecutionStatus;
use App\Models\{Agent, Execution};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Execution>
 */
class ExecutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agent_id'    => Agent::factory(),
            'status'      => ExecutionStatus::Success,
            'duration_ms' => fake()->numberBetween(200, 5000),
            'metadata'    => ['channel' => fake()->randomElement(['whatsapp', 'web', 'api'])],
            'created_at'  => fake()->dateTimeBetween(now()->startOfMonth(), 'now'),
        ];
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExecutionStatus::Failed,
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => ExecutionStatus::Blocked,
            'duration_ms' => null,
        ]);
    }

    public function lastMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween(
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth(),
            ),
        ]);
    }
}
