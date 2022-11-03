<?php

namespace App\Discord\Timeout;

/**
 * Helper class for Timeouts to abstract some code.
 */
class TimeoutHelper
{
    public static function timeoutLength($timeout)
    {
        $length = $timeout->length;
        if ($length >= 60) {
            $length = round($length / 60);
            return "**User**: <@{$timeout->discord_id}>\n**Length**: {$length} hours\n**By**: {$timeout->giver->tag()}\n**Reason**:\n{$timeout->reason}\n\n";
        } else {
            return "**User**: <@{$timeout->discord_id}>\n**Length**: {$length} minutes\n**By**: {$timeout->giver->tag()}\n**Reason**:\n{$timeout->reason}\n\n";
        }
    }

}
