<?php

namespace App\Discord\Core\Command;

use App\Discord\Core\Bot;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

/**
 * Extendable class to easily create new slash and message commands in one. For better understanding:
 * @see Command
 * @see SlashCommandTrait
 * @see MessageCommandTrait
 *
 * The class will only send Embeds back to discord, nothing else. There is an EmbedBuilder and EmbedFactory to more
 * quickly and easily return Embeds
 * @see EmbedBuilder
 * @see EmbedFactory
 */
abstract class SlashAndMessageCommand extends Command
{
    use MessageCommandTrait, SlashCommandTrait;

    public abstract function action(): MessageBuilder;

    /**
     * I would love to move this code to the trait like I did with slash commands, but depending on where the data is going
     * (message only command, or both slash and message command) something else needs to be done.
     * @return void
     *
     * @TODO Fix duplicate code with block below
     */
    public function registerMessageCommand(): void
    {
        Bot::get()->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            if (str_contains($message->content, $discord->user->id)) {
                return;
            }

            if (str_starts_with($message->content, Bot::get()->getPrefix() . $this->trigger)) {
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
                $this->message = $message;
                $this->commandUser = $message->author->id;
                $message->channel->sendMessage($this->action());
            }
        });
    }
}
