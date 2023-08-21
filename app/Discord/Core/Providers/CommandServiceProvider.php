<?php

namespace App\Discord\Core\Providers;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\ServiceProvider;
use App\Discord\Core\SlashCommand;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Command\Command;
use Exception;

class CommandServiceProvider implements ServiceProvider
{
    private Bot $bot;
    private array $globalCommands;
    private array $guildCommands;


    public function __construct()
    {
        $this->guildCommands = config('commands.guild');
        $this->globalCommands = config('commands.global');
    }

    public function boot(Bot $bot): void
    {
        // Silence is golden..
    }

    /**
     * @throws Exception
     */
    public function init(Bot $bot): void
    {
        $this->bot = $bot;
        foreach (array_merge($this->globalCommands, $this->guildCommands) as $mainCommand => $subGroups) {
            foreach ($subGroups as $subGroup => $subCommands) {
                if (is_array($subCommands)) {
                    foreach ($subCommands as $subCommand) {
                        $this->bot->addCommand($mainCommand, $subCommand, $subGroup);
                    }
                } else {
                    $this->bot->addCommand($mainCommand, $subCommands, $mainCommand);
                }
            }
        }
    }
}
