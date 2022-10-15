<?php

namespace App\Discord;

enum AccessLevels: int
{
    case GOD = 900;
    case MOD = 500;
    case USER = 100;
}
