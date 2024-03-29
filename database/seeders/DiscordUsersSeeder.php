<?php

namespace Database\Seeders;

use App\Domain\Discord\User;
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
        $ids = [
            '259461260645629953', // berend
            '964503537495191582', // pewpew
        ];


        foreach ($ids as $id) {
            User::factory()->create([
                'discord_id' => $id,
            ]);
        }
    }
}
