<?php

namespace App\Discord\Core;
/**
 * Preset few access levels before we completely make it dynamic in the future.
 */
enum AccessLevels: int
{
    case BOT_OWNER = 1000;
    case SERVER_OWNER = 900;
    case GOD = 800;
    case MOD = 500;
    case USER = 100;
    case NONE = 0;
}
