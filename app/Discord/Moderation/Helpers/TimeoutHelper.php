<?php

namespace App\Discord\Moderation\Helpers;

/**
 * Helper class for Timeouts to abstract some code.
 */
class TimeoutHelper
{
    /**
     * Yeah.. well.. I got nothing to say. <_<
     * @param $timeout
     * @return string
     */
    public static function timeoutLength($timeout)
    {
        $length = (int) $timeout->length;
        $units = 'seconds';

        if ($length >= 59) {
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

        return "**ID** {$timeout->id}\n**User**: <@{$timeout->discord_id}>\n**Date**: {$timeout->created_at}\n**Length**: {$length} {$units}\n**By**: {$timeout->giver->tag()}\n**Reason**:\n{$timeout->reason}\n\n";
    }

}
