<?php

namespace Database\Seeders;

use App\Domain\Discord\Guild;
use App\Domain\Setting\Models\Setting;
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
        'enable_count' => false,
        'enable_mention_responder' => true,
        'enable_qotd_reminder' => false,
        'enable_lvl_msg' => false,
        'enable_join_role' => false,
        'enable_cmd_log' => false,
        'enable_welcome_msg' => false,
        'enable_bump_reminder' => false,
        'log_channel_id' => 0,
        'bump_channel' => 0,
        'qotd_channel' => 0,
        'count_channel' => 0,
        'level_up_channel' => 0,
        'welcome_msg_channel' => 0,
        'qotd_role' => 0,
        'bump_reminder_role' => 0,
        'join_role' => 0,
        'current_count' => 0,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach (Guild::all() as $guild) {
            $this->processSettings($guild);
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
