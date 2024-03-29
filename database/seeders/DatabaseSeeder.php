<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DiscordUsersSeeder::class,
            GuildSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            SettingsSeeder::class,
            LogSettingsSeeder::class,
            MentionResponderSeeder::class,
        ]);
    }
}
