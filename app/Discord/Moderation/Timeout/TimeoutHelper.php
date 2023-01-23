<?php

namespace App\Discord\Moderation\Timeout;

/**
 * Helper class for Timeouts to abstract some code.
 */
class TimeoutHelper
{
    public static function timeoutLength($timeout)
    {
        $length = $timeout->length;
        $units = 'seconds';

        if ($timeout->length >= 3600) {
            $length /= 60;
            $units = 'minutes';
        }
        if ($timeout->length >= 86400) {
            $length = $timeout->length / 24 / 60 / 60;
            $units = 'days';
        }


        return "**User**: <@{$timeout->discord_id}>\n**Length**: {$length} {$units}\n**By**: {$timeout->giver->tag()}\n**Reason**:\n{$timeout->reason}\n\n";
    }

}
