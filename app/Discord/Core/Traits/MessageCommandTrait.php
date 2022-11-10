<?php

namespace App\Discord\Core\Traits;


use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;
use App\Discord\Core\SlashAndMessageCommand;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

trait MessageCommandTrait
{
    /**
     * @param Message $message
     * @return array
     */
    protected function processParameters(Message $message): array
    {
        $parameters = explode(' ', $message->content);
        array_shift($parameters);
        foreach ($parameters as $index => $parameter) {
            if ($message->mentions->first() && str_contains($parameter, $message->mentions->first())) {
                $parameters[$index] = $message->mentions->first()->id;
            }
        }
        return $parameters;
    }

    /**
     * @param Message $message
     * @param Discord $discord
     * @return bool
     * @throws NoPermissionsException
     */
    protected function validateMessage(Message $message, Discord $discord): bool
    {
        if ($message->author->bot ||
            !str_starts_with($message->content, Bot::get()->getPrefix() . $this->trigger) ||
            str_contains($message->content, $discord->user->id)) {
            return false;
        }

        if (!DiscordUser::hasPermission($message->user_id, $message->guild_id, $this->permission->value) && $this->permission->value !== Permission::NONE->value) {
            $message->channel->sendMessage(EmbedFactory::failedEmbed(__("bot.lack-access")));
            return false;
        }

        if ($this->requiresMention && $message->mentions->count() == 0) {
            $message->channel->sendMessage(EmbedFactory::failedEmbed($this->usageString ?? __('bot.provide-mention')));
            return false;
        }
        if ($this->requiredArguments > 0) {
            $parameters = explode(' ', $message->content);
            if (!isset($parameters[$this->requiredArguments])) {
                $message->channel->sendMessage(EmbedFactory::failedEmbed($this->usageString ?? __('bot.provide-arguments', ['count' => $this->requiredArguments])));
                return false;
            }
        }
        return true;
    }

    /**
     * @return void
     */
    public function registerMessageCommand(): void
    {
        Bot::get()->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if (!$this->validateMessage($message, $discord)) {
                return;
            }

            $this->guildId = $message->guild_id;
            $parameters = $this->processParameters($message);
            $this->arguments = $parameters;
            $this->messageString = join(' ', $this->arguments);
            $this->message = $message;
            $this->commandUser = $message->author->id;

            /**
             * Slash commands require and MessageBuilder to be returned to the interaction. Commands which extend the
             * SlashAndMessageCommand wil thus have to return a MessageBuilder. Non-slash message commands do not use
             * this, and you are free to return anything you want at anytime during the command execution.
             */
            if ($this instanceof MessageCommand) {
                $this->action();
            } elseif ($this instanceof SlashAndMessageCommand) {
                $message->channel->sendMessage($this->action());
            }
        });
    }
}
