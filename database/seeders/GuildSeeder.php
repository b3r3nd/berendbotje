<?php

namespace Database\Seeders;

use App\Discord\Core\Models\Guild;
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
