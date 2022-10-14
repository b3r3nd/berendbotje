<?php

namespace App\Discord;

use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\WebSockets\Intents;

class Bot
{
    private Discord $discord;

    /**
     * @throws IntentException
     */
    public function __construct()
    {
        $this->discord = new Discord([
                'token' => config('discord.token'),
                'loadAllMembers' => true,
                'intents' => Intents::getDefaultIntents() | Intents::GUILD_MEMBERS | Intents::MESSAGE_CONTENT
            ]
        );
    }

    public static function setup(): Discord
    {
        $bot = new self();
        $bot->loadCommands();

        return $bot->discord;
    }

    private function loadCommands(): void
    {
        SimpleCommand::create($this->discord, 'ping', 'pong');
        SimpleCommand::create($this->discord, 'test', 'success');

    }

    /**
     * @return Discord
     */
    public function discord(): Discord
    {
        return $this->discord;
    }

}
