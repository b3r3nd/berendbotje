<?php

namespace App\Discord;

class Helper
{
    /**
     * On some index pages its cool to have a top three, although it's a bit annoying to have this code block
     * randomly in foreach loops throughout the code ;)
     * @param $index
     * @return string
     */
    public static function topThree($index)
    {
        if ($index === 0) {
            return "🥇";
        }
        if ($index === 1) {
            return "🥈";
        }
        if ($index === 2) {
            return "🥉";
        }
        return "";
    }

}
