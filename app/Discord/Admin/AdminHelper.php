<?php

namespace App\Discord\Admin;

use App\Models\Admin;
use App\Models\DiscordUser;

/**
 * Helper functions for Admin, helps abstract some code :)
 */
class AdminHelper
{
    public static function validateAdmin($targetUserId, $commandUserId, $guildId)
    {
        if (!DiscordUser::isAdmin($targetUserId, $guildId)) {
            return __('bot.admins.not-exist');
        }

        $targetUser = DiscordUser::getByGuild($targetUserId, $guildId);
        $targetAdmin = $targetUser->admin;


        if (!DiscordUser::hasHigherLevel($commandUserId, $guildId, $targetAdmin->level)) {
            return __('bot.admins.powerful', ['name' => $targetUser->tag()]);
        }
        return $targetAdmin;
    }

}
