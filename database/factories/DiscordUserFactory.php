<?php

namespace Database\Factories;

use App\Discord\Core\Models\DiscordUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Discord\Core\Models\DiscordUser>
 */
class DiscordUserFactory extends Factory
{
    protected $model = DiscordUser::class;
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
