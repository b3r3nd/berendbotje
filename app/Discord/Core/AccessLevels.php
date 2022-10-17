<?php

namespace App\Discord\Core;

enum AccessLevels: int
{
    case GOD = 900;
    case MOD = 500;
    case USER = 100;
}
