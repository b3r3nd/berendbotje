<?php

namespace Database\Seeders;

use App\Domain\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'roles', 'create-role', 'delete-role', 'update-role',
            'permissions', 'attach-permission', 'attach-role',
            'config', 'timeouts', 'media-filter', 'add-cringe', 'delete-cringe',
            'commands', 'reactions', 'role-rewards', 'manage-xp', 'channels',
            'logs', 'add-mention', 'delete-mention', 'manage-mention-groups', 'openai', 'abusers', 'invites',
            'messages', 'reminders'
        ];

        foreach ($permissions as $permission) {
            Permission::factory()->create(['name' => $permission]);
        }
    }
}
