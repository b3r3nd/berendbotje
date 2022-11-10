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
            'xp_voice_count' => 15,
            'xp_voice_cooldown' => 60,
            'enable_xp' => true,
            'enable_voice_xp' => true,
            'enable_emote_counter' => true,
            'enable_role_rewards' => true,
            'enable_bump_counter' => true,
            'enable_reactions' => true,
            'enable_commands' => true,
            'enable_logging' => false,
            'log_channel' => "",
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
