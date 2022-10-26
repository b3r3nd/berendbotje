<?php

namespace App\Discord;

class Helper
{
    /**
     * On some index pages its cool to have a top three, although it's a bit annoying to have this code block
     * randomly in foreach loops throughout the code ;)
     *
     * @param int $index
     * @param int $offset
     * @return string
     */
    public static function indexPrefix(int $index, int $offset = 0): string
    {
        if ($offset === 0) {
            if ($index === 0) {
                return "🥇";
            }
            if ($index === 1) {
                return "🥈";
            }
            if ($index === 2) {
                return "🥉";
            }
        } else {
            $index += $offset;
        }
        $index = $index + 1;
        return "**{$index}. ** ";
    }
}
