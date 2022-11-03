<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\DiscordUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscordUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = DiscordUser::factory()->create([
            'discord_id' => '259461260645629953',
            'guild_id' => '590941503917129743',
        ]);

        Admin::factory()->create([
            'user_id' => $user->id,
            'level' => 1000,
        ]);
    }
}
