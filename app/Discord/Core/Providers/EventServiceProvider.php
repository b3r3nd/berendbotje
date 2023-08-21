<?php

namespace App\Discord\Core\Providers;

use App\Discord\Core\Bot;
use App\Discord\Core\Events\GuildCreate;
use App\Discord\Core\Interfaces\ServiceProvider;


class EventServiceProvider implements ServiceProvider
{
    private array $messageEvents;
    private array $events;

    public function __construct()
    {
        $this->messageEvents = config('events.message');
        $this->events = config('events.events');
    }

    public function boot(Bot $bot): void
    {
        (new GuildCreate($bot))->register();
    }

    public function init(Bot $bot): void
    {
        foreach ($this->events as $class) {
            $instance = new $class($bot);
            $instance->register();
        }
        foreach ($this->messageEvents as $class) {
            $bot->addMessageAction(new $class());
        }
    }
}
