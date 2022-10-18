<?php

namespace App\Discord\Timeout;

/**
 * Helper class for Timeouts to abstract some code.
 */
class TimeoutHelper
{
    public static function timeoutLength($embed, $timeout)
    {
        $length = $timeout->length;
        if ($length >= 60) {
            $length = $length / 60;
            $embed->addField(['name' => $timeout->discord_username . ' - ' . round($length) . ' hours ', 'value' => $timeout->reason]);
        } else {
            $embed->addField(['name' => $timeout->discord_username . ' - ' . round($length) . ' minutes ', 'value' => $timeout->reason]);
        }
        return $embed;
    }

}
