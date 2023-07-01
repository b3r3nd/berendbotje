<?php

namespace App\Discord\Moderation\Helpers;

/**
 * Helper class for Timeouts to abstract some code.
 */
class TimeoutHelper
{
    public static function timeoutLength($timeout)
    {
        $length = $timeout->length;
        $units = 'seconds';

        if ($length >= 60) {
            $length = round($length / 60);
            $units = 'minute(s)';
        }

        if ($length >= 60) {
            $length = round($length / 60);
            $units = 'hour(s)';
        }

        if ($length >= 24) {
            $length = round($length / 24);
            $units = 'day(s)';
        }

        return "**User**: <@{$timeout->discord_id}>\n**Date**: {$timeout->created_at}\n**Length**: {$length} {$units}\n**By**: {$timeout->giver->tag()}\n**Reason**:\n{$timeout->reason}\n\n";
    }

}
