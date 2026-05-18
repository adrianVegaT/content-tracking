<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<State>
 */
class StateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence(),
        ];
    }

    public function capturing(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'capturing',
            'description' => 'Nota en captura',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'pending',
            'description' => 'Nota pendiente de clasificar',
        ]);
    }
}
