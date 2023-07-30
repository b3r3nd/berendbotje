<?php

namespace App\Discord\Core\Events;

use App\Discord\Core\Bot;
use Discord\Discord;

/**
 * @property Bot $bot            Bot the event belongs to.
 * @property Discord $discord    Easy to access discord instance.
 */
abstract class DiscordEvent
{
    protected Bot $bot;
    protected Discord $discord;

    /**
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        $this->discord = $bot->discord;
    }

    abstract public function register();
}
