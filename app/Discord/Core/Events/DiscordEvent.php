<?php

namespace App\Discord\Core\Events;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\WebSockets\Event;

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

    /**
     * Discord event to add listener to
     *
     * @see Event
     * @return string
     */
    abstract public function event(): string;

    /**
     * Register the listener so it calls function directly
     * @return void
     */
    public function register(): void
    {
        $this->discord->on($this->event(), array($this, 'execute'));
    }

}
