<?php

namespace Database\Seeders;

use App\Discord\Core\Models\Guild;
use App\Discord\Logger\Models\LogSetting;
use Illuminate\Database\Seeder;

class LogSettingsSeeder extends Seeder
{
    public array $logSettings = ['joined_server', 'left_server', 'kicked_from_server', 'banned_from_server',
        'unbanned_from_server', 'timeout', 'joined_call', 'left_call', 'switched_call', 'muted_mod_voice',
        'unmuted_mod_voice', 'updated_username', 'message_updated', 'message_deleted', 'invite_created', 'invite_removed',
        'start_stream', 'end_stream', 'start_cam', 'end_cam'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Guild::all() as $guild) {
            $this->processSettings($guild);
        }
    }

    /**
     * @param Guild $guild
     * @return void
     */
    public function processSettings(Guild $guild): void
    {
        foreach ($this->logSettings as $setting) {
            LogSetting::create(['key' => $setting, 'value' => true, 'guild_id' => $guild->id]);
        }
    }
}
