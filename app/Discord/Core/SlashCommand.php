<?php

namespace App\Discord\Core;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\CustomCommands\Models\Command;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

/**
 * Extendable class to easily create new Slash ONLY commands. For better understanding:
 * @see Command
 * @see SlashCommandTrait
 *
 * @property Bot $bot                       Easy reference to the bot this guild runs in
 * @property Discord $discord               Easy reference to the discord instance
 * @property string $permission             Required permission level for this command.
 * @property string $trigger                Trigger for the command, both slash and text.
 * @property string $guildId                String of the Discord Guild ID
 * @property string $commandUser            ID of the user using the command
 * @property string $description            Description for the (slash) command.
 * @property array $slashCommandOptions     Array of all options @see https://discord.com/developers/docs/interactions/application-commands#application-command-object-application-command-option-structure
 * @property Interaction $interaction       Set with the interaction instance which triggered the command
 *
 */
abstract class SlashCommand
{
    protected Bot $bot;
    protected Discord $discord;
    protected Permission $permission;
    protected string $trigger;
    protected array $arguments = [];
    protected string $guildId = '';
    protected string $commandUser;
    protected string $description;
    protected array $slashCommandOptions;
    protected Interaction $interaction;

    abstract public function permission(): Permission;

    abstract public function trigger(): string;

    abstract public function action(): MessageBuilder;

    public function __construct()
    {
        $this->permission = $this->permission();
        $this->trigger = $this->trigger();
    }

    /** @noinspection NotOptimalIfConditionsInspection */
    public function registerSlashCommand(): void
    {
        $optionsArray = [
            'name' => $this->trigger,
            'description' => $this->description ?? $this->trigger
        ];
        if (isset($this->slashCommandOptions)) {
            $optionsArray['options'] = $this->slashCommandOptions;
        }
        $command = new \Discord\Parts\Interactions\Command\Command($this->discord, $optionsArray);
        $this->discord->listenCommand($this->trigger, function (Interaction $interaction) {
            $this->arguments = [];

            if ($interaction->guild_id === null) {
                return $interaction->respondWithMessage(EmbedFactory::failedEmbed($this->discord,'Slash commands dont work in DM'));
            }

            if (!DiscordUser::hasPermission($interaction->member->id, $interaction->guild_id, $this->permission->value) && $this->permission->value !== Permission::NONE->value) {
                return $interaction->respondWithMessage(EmbedFactory::lackAccessEmbed($this->discord, __("bot.lack-access")));
            }


            // Set some data so it is more easily accessible
            $this->commandUser = $interaction->member->id;
            $this->guildId = $interaction->guild_id;
            $this->interaction = $interaction;

            return $interaction->respondWithMessage($this->action());
        });

        $this->discord->application->commands->save($command);
    }


    /**
     * @param string $key
     * @return mixed|null
     */
    public function getOption(string $key): mixed
    {
        return $this->interaction->data->options->get('name', $key)?->value;
    }

    /**
     * @param Bot $bot
     * @return void
     */
    public function setBot(Bot $bot): void
    {
        $this->bot = $bot;
        $this->discord = $bot->discord;
    }
}
