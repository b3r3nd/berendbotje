<?php

namespace Database\Factories;

use App\Domain\Discord\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Discord\User>
 */
class DiscordUserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'discord_id' => $this->faker->randomKey
        ];
    }
}
