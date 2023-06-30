<?php

namespace Database\Factories;

use App\Discord\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Discord\Settings\Models\Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'key' => $this->faker->randomKey,
            'value' => $this->faker->numberBetween(1, 100),
            'guild_id' => $this->faker->randomKey,
        ];
    }
}
