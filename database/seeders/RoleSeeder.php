<?php

namespace Database\Seeders;

use App\Domain\Discord\Guild;
use App\Domain\Discord\User;
use App\Domain\Permission\Models\Permission;
use App\Domain\Permission\Models\Role;
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
        $owner = User::find(1);

        foreach (Guild::all() as $guild) {
            $this->createAdminRole($guild, $owner);
            $this->createModRole($guild);
        }
    }

    /**
     * @param Guild $guild
     * @param User $owner
     * @return void
     */
    public function createAdminRole(Guild $guild, User $owner): void
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
