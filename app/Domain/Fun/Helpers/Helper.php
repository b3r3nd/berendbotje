<?php

namespace App\Domain\Fun\Helpers;

use App\Domain\Message\Models\MentionGroup;

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
        ++$index;
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
        $totalRequiredXp = 0;
        for ($level = 0; $level < 100; $level++) {
            $xpRequired = 5 * ($level ** 2) + (50 * $level) + 100;
            $totalRequiredXp += $xpRequired;
            if ($totalRequiredXp >= $xp) {
                return $level;
            }
        }
        return 0;
    }

    /**
     * @param int $levelArg
     * @return int
     */
    public static function calcRequiredXp(int $levelArg): int
    {
        $totalRequiredXp = 0;
        for ($level = 0; $level < $levelArg; $level++) {
            $xpRequired = 5 * ($level ** 2) + (50 * $level) + 100;
            $totalRequiredXp += $xpRequired;
        }
        return $totalRequiredXp;
    }

    /**
     * @param int $level
     * @return int
     */
    public static function getXpForLevel(int $level): int
    {
        return 5 * ($level ** 2) + (50 * $level) + 100;
    }


    public static function getVoiceLabel($voice): string
    {
        $voice /= 60;
        if ($voice >= 60) {
            $voice = round($voice / 60);
            $voice = "{$voice} hours";
        } else {
            $voice = round($voice);
            $voice = "{$voice} minutes";
        }

        return $voice;
    }

    /**
     * @param int $number
     * @return string
     */
    public static function format(int $number): string
    {
        return number_format($number, 0, ',', '.');
    }

    /**
     * @param MentionGroup|null $mentionGroup
     * @return string
     */
    public static function getGroupName(?MentionGroup $mentionGroup): string
    {
        if (!$mentionGroup) {
            return " ";
        }
        $description = "{$mentionGroup->id} -";

        if (!$mentionGroup->has_role && !$mentionGroup->has_user) {
            $description .= "Non-";
        }
        if ($mentionGroup->has_user) {
            $description .= "**<@{$mentionGroup->name}>**";
        } else if (is_numeric($mentionGroup->name)) {
            $description .= "**<@&{$mentionGroup->name}>**";
        } else {
            $description .= "**{$mentionGroup->name}**";
        }

        $description .= " • {$mentionGroup->multiplier}x \n";

        return $description;
    }
}
