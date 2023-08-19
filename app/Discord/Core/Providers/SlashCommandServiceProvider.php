<?php

namespace App\Discord\Core\Providers;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\ServiceProvider;
use App\Discord\Core\SlashCommand;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Command\Command;
use Exception;

class SlashCommandServiceProvider implements ServiceProvider
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
        if ($bot->needCommandDeletion()) {
            $this->deleteSlashCommands();
        }
        if ($bot->needsCommandUpdate()) {
            $this->updateSlashCommands($this->globalCommands);
            $this->updateSlashCommands($this->guildCommands, true);
        }
    }

    /**
     * @param array $commands
     * @param bool $guildCommand
     * @return void
     * @throws Exception
     */
    public function updateSlashCommands(array $commands, bool $guildCommand = false): void
    {
        foreach ($commands as $mainCommand => $subGroups) {
            $subGroupOptions = [];
            foreach ($subGroups as $subGroup => $subCommands) {
                if (is_array($subCommands)) {
                    $subCommandOptions = [];
                    foreach ($subCommands as $subCommand) {
                        $subCommandOptions[] = $this->initCommandOptions($mainCommand, $subCommand, $subGroup);
                    }
                    $subGroupOptions[] = [
                        'name' => $subGroup,
                        'description' => $subGroup,
                        'type' => 2,
                        'options' => $subCommandOptions,
                    ];
                } else {
                    $subGroupOptions[] = $this->initCommandOptions($mainCommand, $subCommands, $mainCommand);
                }
            }
            $optionsArray = [
                'name' => $mainCommand,
                'description' => $mainCommand,
                'options' => $subGroupOptions,
            ];


            $command = new Command($this->bot->discord, $optionsArray);
            if ($guildCommand) {
                $this->bot->discord->guilds->fetch(config('discord.support-guild'))->done(function (Guild $guild) use ($command) {
                    $guild->commands->save($command);
                });
            } else {
                $this->bot->discord->application->commands->save($command);
            }
        }
    }

    /**
     * @param $mainCommand
     * @param $command
     * @param $subGroup
     * @return array
     */
    private function initCommandOptions($mainCommand, $command, $subGroup): array
    {
        /** @var SlashCommand $instance */
        $instance = new $command();
        $instance->setBot($this->bot);
        $commandLabel = "{$subGroup}_{$instance->trigger}";
        if ($mainCommand === $subGroup) {
            $instance->setCommandLabel($commandLabel);
        } else {
            $instance->setCommandLabel("{$mainCommand}_{$commandLabel}");
        }
        $this->bot->addSlashCommand($instance, $commandLabel);
        $options = [
            'name' => $instance->trigger,
            'description' => $instance->description,
            'type' => 1,
        ];
        if (isset($instance->slashCommandOptions)) {
            $options['options'] = $instance->slashCommandOptions;
        }
        return $options;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function deleteSlashCommands(): void
    {
        $this->bot->discord->application->commands->freshen()->done(function ($commands) {
            foreach ($commands as $command) {
                $this->bot->discord->application->commands->delete($command);
            }
        });
    }
}
