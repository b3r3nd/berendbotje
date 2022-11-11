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


    /**
     * Calculate the level bases on amount of xp
     *
     * 500 * (level^2) - (500 * level) to calculate level based on XP.
     * Xp is as simple as xx amount of XP per message. (15 by default)
     *
     * @param int $xp
     * @return int
     */
    public static function calcLevel(int $xp): int
    {
        for ($level = 0; $level < 1000; $level++) {
            $xpRequired = 500 * pow($level, 2) - (500 * $level);
            if ($xp < $xpRequired) {
                return $level;
            }
        }
        return 0;
    }
}
