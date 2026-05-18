<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Note;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'channel_id' => Channel::factory(),
            'state_id' => State::factory(),
            'content' => fake()->paragraph(),
        ];
    }

    public function capturing(): static
    {
        return $this->state(fn (array $attributes) => [
            'state_id' => State::firstOrCreate(
                ['name' => 'capturing'],
                ['description' => 'Nota en captura']
            )->id,
            'content' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'state_id' => State::firstOrCreate(
                ['name' => 'pending'],
                ['description' => 'Nota pendiente de clasificar']
            )->id,
        ]);
    }
}
