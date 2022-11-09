<?php

namespace App\Discord\Core\Command;

use App\Discord\Core\Bot;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\DiscordUser;
use Discord\Parts\Interactions\Interaction;
use Exception;

/**
 * @property string $description            Description for the (slash) command.
 * @property array $slashCommandOptions     Array of all options @see https://discord.com/developers/docs/interactions/application-commands#application-command-object-application-command-option-structure
 */
trait SlashCommandTrait
{
    protected string $description;
    protected array $slashCommandOptions;

    /**
     * @return void
     * @throws Exception
     */
    public function registerSlashCommand(): void
    {
        $optionsArray = [
            'name' => $this->trigger,
            'description' => $this->description ?? $this->trigger
        ];
        if (isset($this->slashCommandOptions)) {
            $optionsArray['options'] = $this->slashCommandOptions;
        }
        $command = new \Discord\Parts\Interactions\Command\Command(Bot::getDiscord(), $optionsArray);
        Bot::getDiscord()->listenCommand($this->trigger, function (Interaction $interaction) {
            $this->arguments = [];

            if (!DiscordUser::hasPermission($interaction->member->id, $interaction->guild_id, $this->permission->value) && $this->permission->value !== Permission::NONE->value) {
                return $interaction->respondWithMessage(EmbedFactory::failedEmbed(__("bot.lack-access")));
            }

            foreach ($interaction->data->options as $option) {
                $this->arguments[] = $option->value;
            }
            $this->commandUser = $interaction->member->id;
            $this->guildId = $interaction->guild_id;
            return $interaction->respondWithMessage($this->action());
        });

        // Why you ask? I do not want to register slash commands everytime on my test bot. It takes time, it's
        // Annoying and I don't need them!
        if (env('APP_ENV') != 'local') {
            Bot::getDiscord()->application->commands->save($command);
        }
    }

    /**
     * @return string
     */
    public function getCommandUser(): string
    {
        return $this->commandUser;
    }
}
