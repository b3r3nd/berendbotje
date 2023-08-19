<?php

namespace App\Discord\Core;

use App\Discord\Core;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Discord\Core\Providers\EventServiceProvider;
use App\Discord\Core\Providers\GuildServiceProvider;
use App\Discord\Core\Providers\SlashCommandServiceProvider;
use App\Domain\Discord\Guild;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\WebSockets\Intents;
use Exception;

class Bot
{
    /**
     * @see \App\Discord\Core\Interfaces\ServiceProvider
     * @var array|string[]
     */
    private array $serviceProviders = [
        EventServiceProvider::class,
        GuildServiceProvider::class,
        SlashCommandServiceProvider::class,
    ];

    public function __construct(
        private readonly bool $updateCommands,
        private readonly bool $deleteCommands,
        public ?Discord       $discord = null,
        public array          $guilds = [],
        public array          $messageActions = [],
        public array          $commands = [],
        private array         $services = [],
    )
    {
        foreach ($this->serviceProviders as $serviceProvider) {
            $this->services[] = new $serviceProvider();
        }
    }

    /**
     * @return void
     * @throws IntentException
     */
    public function connect(): void
    {
        $this->discord = new Discord([
                'token' => config('discord.token'),
                'loadAllMembers' => true,
                'storeMessages' => true,
                'intents' => Intents::getDefaultIntents() | Intents::GUILD_VOICE_STATES | Intents::GUILD_MEMBERS |
                    Intents::MESSAGE_CONTENT | Intents::GUILDS | Intents::GUILD_INVITES | Intents::GUILD_EMOJIS_AND_STICKERS
            ]
        );
        $this->discord->on('init', function (Discord $discord) {
            foreach ($this->services as $service) {
                $service->init($this);
            }
        });
        foreach ($this->services as $service) {
            $service->boot($this);
        }
        $this->discord->run();
    }

    /**
     * @param MessageCreateAction $action
     * @return void
     */
    public function addMessageAction(MessageCreateAction $action): void
    {
        $this->messageActions[] = $action;
    }

    /**
     * @param SlashCommand $command
     * @param string $trigger
     * @return void
     */
    public function addSlashCommand(SlashCommand $command, string $trigger): void
    {
        $this->commands[$trigger] = $command;
    }

    /**
     * @param Guild $guild
     * @return void
     * @throws Exception
     */
    public function addGuild(Guild $guild): void
    {
        if (!isset($this->guilds[$guild->guild_id])) {
            $this->guilds[$guild->guild_id] = new Core\Guild($guild, $this);
        }
    }

    /**
     * @param string $id
     * @return mixed|null
     */
    public function getGuild(string $id): ?Core\Guild
    {
        return $this->guilds[$id] ?? null;
    }

    /**
     * @return array
     */
    public function getGuilds(): array
    {
        return $this->guilds;
    }

    /**
     * @return bool
     */
    public function needsCommandUpdate(): bool
    {
        return $this->updateCommands;
    }

    /**
     * @return bool
     */
    public function needCommandDeletion(): bool
    {
        return $this->deleteCommands;
    }
}
