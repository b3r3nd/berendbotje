<?php

namespace Database\Seeders;

use App\Discord\Core\Models\Guild;
use App\Discord\Core\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public array $settings = [
        'xp_count' => 20,
        'xp_cooldown' => 60,
        'xp_voice_count' => 4,
        'xp_voice_cooldown' => 60,
        'enable_xp' => true,
        'enable_voice_xp' => true,
        'enable_emote_counter' => true,
        'enable_role_rewards' => false,
        'enable_bump_counter' => false,
        'enable_reactions' => true,
        'enable_commands' => false,
        'enable_logging' => false,
        'log_channel_id' => 0,
        'enable_bump_reminder' => false,
        'bump_reminder_role' => 0,
        'bump_channel' => 0,
        'enable_mention_responder' => false,
        'enable_qotd_reminder' => false,
        'qotd_channel' => 0,
        'qotd_role' => 0,
        'count_channel' => 0,
        'current_count' => 0,
        'enable_count' => false,
        'level_up_channel' => 0,
        'enable_lvl_msg' => false,
        'enable_join_role' => false,
        'join_role' => 0,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach (Guild::all() as $guild) {
            foreach ($this->settings as $key => $value) {
                $this->processSettings($guild);
            }
        }
    }

    /**
     * @param Guild $guildModel
     * @return void
     */
    public function processSettings(Guild $guildModel): void
    {
        foreach ($this->settings as $key => $value) {
            Setting::factory()->create([
                'key' => $key,
                'value' => $value,
                'guild_id' => $guildModel->id,
            ]);
        }
    }
}
