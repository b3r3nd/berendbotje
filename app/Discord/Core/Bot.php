<?php

namespace App\Discord\Core;

use App\Discord\Core;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Discord\Core\Providers\EventServiceProvider;
use App\Discord\Core\Providers\GuildServiceProvider;
use App\Discord\Core\Providers\CommandServiceProvider;
use App\Domain\Discord\Guild;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\WebSockets\Intents;
use Exception;

class Bot
{
    public function __construct(
        public ?Discord $discord = null,
        public array    $guilds = [],
        public array    $messageActions = [],
        public array    $commands = [],
        private array   $services = [],
    )
    {
        foreach (config('discord.providers') as $serviceProvider) {
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
     * @param $mainCommand
     * @param $command
     * @param $subGroup
     * @return void
     */
    public function addCommand($mainCommand, $command, $subGroup): void
    {
        $instance = new $command();
        $instance->setBot($this);
        $commandLabel = "{$subGroup}_{$instance->trigger}";
        if ($mainCommand === $subGroup) {
            $instance->setCommandLabel($commandLabel);
        } else {
            $instance->setCommandLabel("{$mainCommand}_{$commandLabel}");
        }
        $this->commands[$commandLabel] = $instance;
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
}
