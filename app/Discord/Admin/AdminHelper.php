<?php

namespace App\Discord\Admin;

use App\Models\Admin;
use App\Models\DiscordUser;

/**
 * Helper functions for Admin, helps abstract some code :)
 */
class AdminHelper
{
    public static function validateAdmin($adminId, $userId)
    {
        $user = DiscordUser::where('discord_id', $adminId)->first();

        if (!DiscordUser::isAdmin($adminId)) {
            return __('bot.admins.not-exist');
        }
        $admin = $user->admin;

        if (!DiscordUser::hasHigherLevel($userId, $admin->level)) {
            return __('bot.admins.powerful', ['name' => $user->discord_tag]);
        }
        return $admin;
    }

}
