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

/**
 * @property Discord $discord               Set with the global discord instance from DiscordPHP.
 * @property array $guilds                  List of all active guilds using the bot.
 * @property bool $updateCommands           If we need to update commands.
 * @property bool $deleteCommands           If we need to delete commands.
 * @property array $messageActions          List with action instances to execute on MESSAGE_CREATE.
 * @property array $commands                List of slash command instances active in the bot.
 * @property array $services                List of active services (Service Providers)
 */
class Bot
{
    public Discord $discord;
    private array $guilds;
    private bool $updateCommands, $deleteCommands;
    public array $messageActions = [];
    public array $commands = [];
    private array $services = [];

    /**
     * @see \App\Discord\Core\Interfaces\ServiceProvider
     * @var array|string[]
     */
    private array $serviceProviders = [
        EventServiceProvider::class,
        GuildServiceProvider::class,
        SlashCommandServiceProvider::class,
    ];


    /**
     * @param bool $updateCommands
     * @param bool $deleteCommands
     */
    public function __construct(bool $updateCommands = false, bool $deleteCommands = false)
    {
        $this->updateCommands = $updateCommands;
        $this->deleteCommands = $deleteCommands;

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
