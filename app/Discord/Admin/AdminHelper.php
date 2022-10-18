<?php

namespace App\Discord\Admin;

use App\Models\Admin;

/**
 * Helper functions for Admin, helps abstract some code :)
 */
class AdminHelper
{
    public static function validateAdmin($adminId, $userId) {
        $admin = Admin::where(['discord_id' => $adminId])->first();
        if (!$admin) {
           return __('bot.admins.not-exist');
        }
        if (!Admin::hasHigherLevel($userId, $admin->level)) {
           return __('bot.admins.powerful', ['name' => $admin->discord_username]);
        }
        return $admin;
    }

}
