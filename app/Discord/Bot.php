<?php

namespace App\Discord;

use App\Models\Command;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\WebSockets\Intents;

class Bot
{
    private Discord $discord;
    private string $prefix = '$';


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
        foreach (Command::all() as $command) {
            SimpleCommand::create($this->discord, $command->trigger, $command->response);
        }

        new SimpleCommandCRUD($this->discord, $this);
    }

    /**
     * @return Discord
     */
    public function discord(): Discord
    {
        return $this->discord;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

}
