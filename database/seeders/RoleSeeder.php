<?php

namespace Database\Seeders;

use App\Models\DiscordUser;
use App\Models\Guild;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['admin'];

        foreach ($roles as $role) {
            foreach (Guild::all() as $guild) {
                $tmpRole = Role::factory()->create([
                    'name' => $role,
                    'guild_id' => $guild->id,
                ]);

                $tmpRole->permissions()->attach(Permission::all());
                $user = DiscordUser::find(1);
                $user->roles()->attach($tmpRole);
            }
        }
    }
}
