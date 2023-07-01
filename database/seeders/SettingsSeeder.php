<?php

namespace Database\Seeders;

use App\Discord\Core\Models\Guild;
use App\Discord\Core\Models\Setting;
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
            'xp_count' => 20,
            'xp_cooldown' => 60,
            'xp_voice_count' => 4,
            'xp_voice_cooldown' => 60,
            'enable_xp' => true,
            'enable_voice_xp' => true,
            'enable_emote_counter' => true,
            'enable_role_rewards' => true,
            'enable_bump_counter' => true,
            'enable_reactions' => true,
            'enable_commands' => true,
            'enable_logging' => false,
            'log_channel_id' => "",
            'enable_bump_reminder' => false,
            'bump_reminder_role' => "",
            'bump_channel' => "",
            'enable_mention_responder' => true,
            'enable_qotd_reminder' => true,
            'qotd_channel' => "",
            'qotd_role' => "",
            'count_channel' => "",
            'current_count' => 0,
            'enable_count' => true,
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
