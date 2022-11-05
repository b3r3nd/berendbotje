<?php

namespace Database\Seeders;

use App\Discord\Core\PermissionScope;
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

        $user = DiscordUser::find(1);
        foreach ($roles as $role) {
            foreach (Guild::all() as $guild) {
                $tmpRole = Role::factory()->create([
                    'name' => $role,
                    'guild_id' => $guild->id,
                ]);

                $tmpRole->permissions()->attach(Permission::all());
                $user->roles()->attach($tmpRole);
            }
        }
        $guild = Guild::find(1);
        $role = Role::factory()->create([
            'name' => 'owners',
            'guild_id' => $guild->id,
            'is_admin' => true,
        ]);
        $role->permissions()->attach(Permission::withoutGlobalScope(PermissionScope::class)->get());
        $user->roles()->attach($role);
    }

}
