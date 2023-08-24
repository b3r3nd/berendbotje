<?php

namespace App\Discord\Levels\Helpers;

use Illuminate\Support\Carbon;

class Helper
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


}
