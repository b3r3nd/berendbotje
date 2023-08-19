<?php

namespace App\Discord\Core;

use Discord\Discord;

abstract class DiscordEvent
{
    /**
     * @param Bot $bot
     * @param Discord|null $discord
     */
    public function __construct(
        protected Bot      $bot,
        protected ?Discord $discord = null,
    )
    {
        $this->discord = $bot->discord;
    }

    /**
     * @return string
     * @see Event
     */
    abstract public function event(): string;

    /**
     * @return void
     */
    public function register(): void
    {
        $this->discord->on($this->event(), array($this, 'execute'));
    }

}
