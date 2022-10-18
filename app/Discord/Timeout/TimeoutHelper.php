<?php

namespace App\Discord\Timeout;

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
