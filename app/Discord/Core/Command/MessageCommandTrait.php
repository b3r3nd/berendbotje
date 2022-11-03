<?php

namespace App\Discord\Core\Command;


use App\Discord\Core\Bot;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;

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
        if (!DiscordUser::hasLevel($message->author->id, $this->accessLevel->value)) {
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
}
