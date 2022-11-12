<?php

namespace Database\Seeders;

use App\Models\Guild;
use App\Models\LogSetting;
use Illuminate\Database\Seeder;

class LogSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $logSettings = ['joined_server', 'left_server', 'kicked_from_server', 'banned_from_server',
            'unbanned_from_server', 'timeout', 'joined_call', 'left_call', 'switched_call', 'muted_mod_voice',
            'unmuted_mod_voice', 'updated_username', 'message_updated', 'message_deleted', 'invite_created', 'invite_removed',
            'start_stream', 'end_stream', 'start_cam', 'end_cam'
        ];

        foreach (Guild::all() as $guild) {
            foreach ($logSettings as $setting) {
                LogSetting::create(['key' => $setting, 'value' => true, 'guild_id' => $guild->id]);
            }
        }
    }
}
