<?php

namespace Database\Factories;

use App\Domain\Discord\Guild;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Discord\Guild>
 */
class GuildFactory extends Factory
{
    protected $model = Guild::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'guild_id' => $this->faker->randomKey,
            'owner_id' => $this->faker->randomKey,
        ];
    }
}
