<?php

namespace App\Discord\Core;

use Discord\Discord;

abstract class DiscordEvent
{
    protected Bot $bot;
    protected Discord $discord;

    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        $this->discord = $bot->discord;
    }

    abstract public function registerEvent();
}
