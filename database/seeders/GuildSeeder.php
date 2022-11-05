<?php

namespace Database\Seeders;

use App\Models\Guild;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guilds = [
            [
                // First guild is owner guild
                'name' => 'BerendBotje',
                'guild_id' => '1034615413725736970',
                'owner_id' => 1, // 1 = main admin account
            ],
            [
                'name' => 'Netherlands',
                'guild_id' => '590941503917129743',
                'owner_id' => 1,
            ],
        ];

        foreach ($guilds as $guild) {
            Guild::factory()->create($guild);
        }
    }
}
