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
        $roles = ['Admin'];

        foreach ($roles as $role) {
            foreach (Guild::all() as $guild) {
                $role = Role::factory()->create([
                    'name' => $role,
                    'guild_id' => $guild->id,
                ]);

                $role->permissions()->attach(Permission::all());

                foreach (DiscordUser::all() as $user) {
                    $user->roles()->attach($role);
                }
            }
        }
    }
}
