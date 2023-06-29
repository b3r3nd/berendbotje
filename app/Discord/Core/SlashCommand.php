<?php

namespace App\Discord\Core;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

/**
 * Extendable class to easily create new Slash ONLY commands. For better understanding:
 * @see Command
 * @see SlashCommandTrait
 *
 * @property string $description            Description for the (slash) command.
 * @property array $slashCommandOptions     Array of all options @see https://discord.com/developers/docs/interactions/application-commands#application-command-object-application-command-option-structure
 * @property Interaction $interaction       Set with the interaction instance which triggered the command
 */
abstract class SlashCommand extends Command
{
    protected string $description;
    protected array $slashCommandOptions;
    protected Interaction $interaction;

    abstract public function action(): MessageBuilder;

    /** @noinspection NotOptimalIfConditionsInspection */
    public function registerSlashCommand(Discord $discord): void
    {
        $optionsArray = [
            'name' => $this->trigger,
            'description' => $this->description ?? $this->trigger
        ];
        if (isset($this->slashCommandOptions)) {
            $optionsArray['options'] = $this->slashCommandOptions;
        }
        $command = new \Discord\Parts\Interactions\Command\Command($discord, $optionsArray);
        $discord->listenCommand($this->trigger, function (Interaction $interaction) {
            $this->arguments = [];

            if ($interaction->guild_id === null) {
                return $interaction->respondWithMessage(EmbedFactory::failedEmbed('Slash commands dont work in DM'));
            }

            if (!DiscordUser::hasPermission($interaction->member->id, $interaction->guild_id, $this->permission->value) && $this->permission->value !== Permission::NONE->value) {
                return $interaction->respondWithMessage(EmbedFactory::lackAccessEmbed(__("bot.lack-access")));
            }

            // Left over from previous stucture, should probably delete this and access data directly from the interaction!
            foreach ($interaction->data->options as $option) {
                $this->arguments[] = $option->value;
            }
            $this->commandUser = $interaction->member->id;
            $this->guildId = $interaction->guild_id;


            $this->interaction = $interaction;

            return $interaction->respondWithMessage($this->action());
        });

        $discord->application->commands->save($command);
    }

}
