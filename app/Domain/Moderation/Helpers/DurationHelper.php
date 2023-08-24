<?php

namespace App\Domain\Moderation\Helpers;

use Illuminate\Support\Carbon;

class DurationHelper
{
    /**
     * @param string $duration
     * @return mixed
     */
    public static function match(string $duration): mixed
    {
        preg_match('/(?<year>\d+y)?\/?(?<month>\d+m)?\/?(?<day>\d+d)?/', $duration, $matches);
        return $matches;
    }

    /**
     * @param $date
     * @return bool|Carbon
     */
    public static function parse($date): bool|Carbon
    {
        try {
            return Carbon::parse($date);
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            return false;
        }
    }

    /**
     * @param string $string
     * @return int
     */
    public static function cast(string $string): int
    {
        return (int)substr($string, 0, -1);
    }

    /**
     * @param $matches
     * @return Carbon
     */
    public static function getDate($matches): Carbon
    {
        $date = now();

        if (isset($matches['year'])) {
            $date->subYears(self::cast($matches['year']));
        }
        if (isset($matches['month'])) {
            $date->subMonths(self::cast($matches['month']));
        }
        if (isset($matches['day'])) {
            $date->subDays(self::cast($matches['day']));
        }

        return $date;
    }
}
