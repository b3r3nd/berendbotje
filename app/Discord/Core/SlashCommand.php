<?php

namespace App\Discord\Core;

use App\Discord\Core\Builders\EmbedFactory;
use App\Domain\Discord\User;
use App\Domain\Permission\Enums\Permission;
use App\Domain\Setting\Enums\Setting;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Exception;

abstract class SlashCommand
{
    public string $description = "";
    public array $slashCommandOptions = [];

    /**
     * @return Permission
     * @see Setting
     */
    abstract public function permission(): Permission;

    /**
     * @return string
     */
    abstract public function trigger(): string;

    /**
     * @return MessageBuilder
     */
    abstract public function action(): MessageBuilder;

    /**
     * @param Interaction $interaction
     * @return array
     */
    abstract public function autoComplete(Interaction $interaction): array;


    public function __construct(
        public ?Bot           $bot = null,
        public ?Discord       $discord = null,
        protected ?Permission $permission = null,
        public ?string        $trigger = null,
        protected ?string     $guildId = null,
        public ?Interaction   $interaction = null,
        public ?string        $commandLabel = null,
    )
    {
        $this->permission ??= $this->permission();
        $this->trigger ??= $this->trigger();
    }


    /**
     * @param Interaction $interaction
     * @return void
     */
    public function complete(Interaction $interaction): void
    {
        $this->interaction = $interaction;
        $interaction->autoCompleteResult($this->autoComplete($interaction));
    }

    /**
     * @param Interaction $interaction
     * @throws Exception
     */
    public function execute(Interaction $interaction): void
    {
        $this->interaction = $interaction;
        if ($interaction->guild_id === null) {
            $interaction->respondWithMessage(EmbedFactory::failedEmbed($this, __('bot.log.no-dm')));
        }
        $this->guildId = $interaction->guild_id;
        $guild = $this->bot->getGuild($interaction->guild_id);
        if ($this->permission !== Permission::NONE && !User::hasPermission($interaction->member->id, $interaction->guild_id, $this->permission)) {
            if ($guild->getSetting(Setting::ENABLE_CMD_LOG)) {
                $guild->logWithMember($interaction->member, __('bot.log.failed', ['trigger' => $this->commandLabel]), 'fail');
            }
            $interaction->respondWithMessage(EmbedFactory::lackAccessEmbed($this, __("bot.lack-access")));
            return;
        }
        if ($guild->getSetting(Setting::ENABLE_CMD_LOG)) {
            $guild->logWithMember($interaction->member, __('bot.log.success', ['trigger' => $this->commandLabel]), 'success');
        }
        $interaction->respondWithMessage($this->action());
    }


    /**
     * @param string $key
     * @return mixed|null
     */
    public function getOption(string $key): mixed
    {
        $option = $this->interaction->data->options->first();
        if ($option->options->get('name', $key) === null) {
            if ($option->options->first()) {
                return $option->options->first()->options->get('name', $key)->value;
            }
            return null;
        }
        return $option->options->get('name', $key)->value;
    }

    /**
     * We have autocomplete for a lot of Models when using them in slash commands, they can all call this function to
     * easily retrieve and map the matching instances.
     *
     * You cannot show more than 25 options at a time and the name of the option cannot be longer than 32 characters.
     *
     * @param string $model
     * @param string $guidId
     * @param string $optionKey
     * @param string $optionValue
     * @param string|null $optionLabel
     * @return mixed
     *
     * @noinspection PhpUndefinedMethodInspection
     */
    public function getAutoComplete(string $model, string $guidId, string $optionKey, string $optionValue = '', string $optionLabel = null): mixed
    {
        return $model::byGuild($guidId)->where($optionKey, 'LIKE', "%{$optionValue}%")
            ->limit(25)
            ->get()
            ->map(function ($modelInstance) use ($optionKey, $optionLabel) {
                return ['name' => substr($modelInstance->{$optionLabel ?? $optionKey}, 0, 32), 'value' => $modelInstance->{$optionKey}];
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

    /**
     * @param string $commandLabel
     * @return void
     */
    public function setCommandLabel(string $commandLabel): void
    {
        $this->commandLabel = str_replace('_', ' ', $commandLabel);
    }
}
