<?php

namespace App\Discord\Core;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Extendable class to easily create new Slash commands.
 *
 * @property Bot $bot                       Easy reference to the bot this guild runs in.
 * @property Discord $discord               Easy reference to the discord instance.
 * @property string $permission             Required permission level for this command.
 * @property string $trigger                Trigger for the command, both slash and text.
 * @property string $guildId                String of the Discord Guild ID
 * @property string $description            Description for the (slash) command.
 * @property array $slashCommandOptions     Array of all options @see https://discord.com/developers/docs/interactions/application-commands#application-command-object-application-command-option-structure
 * @property Interaction $interaction       Set with the interaction instance which triggered the command
 *
 */
abstract class SlashCommand
{
    public Bot $bot;
    public Discord $discord;
    protected Permission $permission;
    protected string $trigger;
    protected string $guildId = '';
    protected string $description;
    protected array $slashCommandOptions;
    public Interaction $interaction;

    /**
     * Permissions required for using this command, you are required to use the Setting Enum
     *
     * @return Permission
     * @see Setting
     *
     */
    abstract public function permission(): Permission;

    /**
     * The actual name/trigger of the command used by users
     *
     * @return string
     */
    abstract public function trigger(): string;

    /**
     * Execute the command and return a proper MessageBuilder to send back to discord
     *
     * @return MessageBuilder
     */
    abstract public function action(): MessageBuilder;

    /**
     * If you are using slash command options, this function will receive when the user is typing,
     * so you can find options matching what the user is searching for and display them!
     *
     * @param Interaction $interaction
     * @return array
     */
    abstract public function autoComplete(Interaction $interaction): array;

    public function __construct()
    {
        $this->permission = $this->permission();
        $this->trigger = $this->trigger();
    }

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
        $command = new \Discord\Parts\Interactions\Command\Command($this->discord, $optionsArray);
        $this->discord->listenCommand($this->trigger, function (Interaction $interaction) {
            $this->interaction = $interaction;
            if ($interaction->guild_id === null) {
                return $interaction->respondWithMessage(EmbedFactory::failedEmbed($this, __('bot.log.no-dm')));
            }
            $this->guildId = $interaction->guild_id;
            $guild = $this->bot->getGuild($interaction->guild_id);
            if ($this->permission->value !== Permission::NONE->value && !DiscordUser::hasPermission($interaction->member->id, $interaction->guild_id, $this->permission->value)) {
                $guild->logWithMember($interaction->member, __('bot.log.failed', ['trigger' => $this->trigger]), 'fail');
                return $interaction->respondWithMessage(EmbedFactory::lackAccessEmbed($this, __("bot.lack-access")));
            }
            $guild->logWithMember($interaction->member, __('bot.log.success', ['trigger' => $this->trigger]), 'success');
            return $interaction->respondWithMessage($this->action());
        }, function (Interaction $interaction) {
            $this->interaction = $interaction;
            return $this->autoComplete($interaction);
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
     * We have autocomplete for a lot of Models when using them in slash commands, they can all call this function to
     * easily retrieve and map the matching instances.
     *
     * @param string $model
     * @param string $guidId
     * @param string $optionKey
     * @param string $optionValue
     * @return mixed
     *
     * @noinspection PhpUndefinedMethodInspection
     */
    public function getAutoComplete(string $model, string $guidId, string $optionKey, string $optionValue): mixed
    {
        return $model::byGuild($guidId)->where($optionKey, 'LIKE', "%{$optionValue}%")
            ->limit(25)
            ->get()
            ->map(function ($modelInstance) use ($optionKey) {
                return ['name' => $modelInstance->{$optionKey}, 'value' => $modelInstance->{$optionKey}];
            })->toArray();
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
