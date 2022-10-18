<?php

namespace App\Discord\Core;
/**
 * Preset few access levels before we completely make it danmic in the future.
 */
enum AccessLevels: int
{
    case NONE = 0;
    case GOD = 900;
    case MOD = 500;
    case USER = 100;
}
