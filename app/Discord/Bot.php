<?php

namespace App\Discord;

use App\Models\Command;
use App\Models\Reaction;
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
            SimpleCommand::create($this, $command->trigger, $command->response);
        }

        foreach (Reaction::all() as $command) {
            SimpleReaction::create($this, $command->trigger, $command->reaction);
        }

        new SimpleCommandCRUD($this);
        new AdminManagement($this);
        new SimpleReactionsCRUD($this);
        new BumpCounter($this);
        new BumpStatistics($this);
        new CringeCounter($this);
        new Timeout($this);
        new DetectTimeouts($this);
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
