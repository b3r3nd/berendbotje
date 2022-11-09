<?php

namespace Database\Seeders;

use App\Models\Guild;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            'xp_count' => 15,
            'xp_cooldown' => 60,
        ];

        foreach (Guild::all() as $guild) {
            foreach ($settings as $key => $value) {
                Setting::factory()->create([
                    'key' => $key,
                    'value' => $value,
                    'guild_id' => $guild->id,
                ]);
            }
        }
    }
}
