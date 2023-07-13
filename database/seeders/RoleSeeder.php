<?php

namespace Database\Seeders;

use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use App\Discord\Roles\Models\Permission;
use App\Discord\Roles\Models\Role;
use App\Discord\Roles\Scopes\PermissionScope;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public array $modPerms = ['timeouts', 'media-filter', 'add-cringe',
        'delete-cringe', 'commands', 'reactions',
        'add-mention', 'channels', 'delete-mention', 'openai'];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $owner = DiscordUser::find(1);

        foreach (Guild::all() as $guild) {
            $this->createModRole($guild);
            $this->createAdminRole($guild, $owner);
        }
    }

    /**
     * @param Guild $guild
     * @param DiscordUser $owner
     * @return void
     */
    public function createAdminRole(Guild $guild, DiscordUser $owner): void
    {
        $adminRole = Role::factory()->create(['name' => 'Administrator', 'guild_id' => $guild->id,]);
        $adminRole->permissions()->attach(Permission::all()->pluck('id'));
        $owner->roles()->attach($adminRole);
    }

    /**
     * @param Guild $guild
     * @return void
     */
    public function createModRole(Guild $guild): void
    {
        $modRole = Role::factory()->create(['name' => 'Moderator', 'guild_id' => $guild->id,]);
        foreach ($this->modPerms as $permName) {
            $modRole->permissions()->attach(Permission::get($permName)->id);
        }
    }

}
