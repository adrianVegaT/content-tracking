<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Channel>
 */
class ChannelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence(),
        ];
    }

    public function telegram(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'telegram',
            'description' => 'Canal de Telegram',
        ]);
    }
}
