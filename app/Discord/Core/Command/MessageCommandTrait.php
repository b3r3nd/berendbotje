<?php

namespace App\Discord\Core\Command;

use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;

/**
 * @property bool $requiresMention          Whether a user mention is required as argument
 * @property int $requiredArguments         The amount of arguments required for the text version of the command.
 * @property string $usageString            Example usage of how to use the command, shown as error on incorrect usage.
 * @property string $messageString          String of the message received without command trigger.
 */
trait MessageCommandTrait
{
    protected bool $requiresMention = false;
    protected int $requiredArguments = 0;
    protected string $usageString;
    protected string $messageString = '';

    /**
     * @param $message
     * @return void
     */
    protected function validateMessage($message): void
    {
        if (!DiscordUser::hasLevel($message->author->id, $this->accessLevel->value)) {
            $message->channel->sendMessage(EmbedFactory::failedEmbed(__("bot.lack-access")));
            return;
        }
        if ($this->requiresMention && $message->mentions->count() == 0) {
            $message->channel->sendMessage(EmbedFactory::failedEmbed($this->usageString ?? __('bot.provide-mention')));
            return;
        }
        if ($this->requiredArguments > 0) {
            $parameters = explode(' ', $message->content);
            if (!isset($parameters[$this->requiredArguments])) {
                $message->channel->sendMessage(EmbedFactory::failedEmbed($this->usageString ?? __('bot.provide-arguments', ['count' => $this->requiredArguments])));
                return;
            } else {
                array_shift($parameters);
                $this->messageString = join(' ', $this->arguments);
                foreach ($parameters as $index => $parameter) {
                    if ($message->mentions->first() && str_contains($parameter, $message->mentions->first())) {
                        $parameters[$index] = $message->mentions->first()->id;
                    }
                }
                $this->arguments = $parameters;
            }
        }

    }
}
