<?php

namespace Database\Factories;

use App\Models\TelegramUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TelegramUser>
 */
class TelegramUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'telegram_chat_id' => fake()->unique()->numerify('##########'),
            'telegram_username' => fake()->userName(),
        ];
    }
}
