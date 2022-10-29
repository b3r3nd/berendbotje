<?php

namespace App\Discord\Core;

use App\Models\Admin;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

/**
 * Abstract command class for text commands. For slash commands @see SlashCommand
 *
 * It does some basic error handling and parameter validation so you do not need to bother with this in child command
 * classes.
 *
 * Properties you can set yourself in child class:
 * @property AccessLevels $accessLevels     Required access level for this command.
 * @property string $trigger                Trigger for the command, both slash and text.
 * @property bool $requiresMention          Whether a user mention is required as argument
 * @property int $requiredArguments         The amount of arguments required for the text version of the command.
 * @property string $usageString            Example usage of how to use the command, shown as error on incorrect usage.
 *
 * Properties which will be set for you and available in child:
 * @property string $message                If available set with the Message instance received.
 * @property array $arguments               Array of all the given arguments by either slash or text commands.
 * @property string $messageString          String of the message received without command trigger.
 */
abstract class Command
{
    protected AccessLevels $accessLevel;
    protected Message $message;
    protected string $command;
    protected string $trigger;
    protected bool $requiresMention = false;
    protected int $requiredArguments = 0;
    protected array $arguments = [];
    protected string $messageString = '';
    protected string $usageString;

    public abstract function accessLevel(): AccessLevels;

    public abstract function trigger(): string;

    public abstract function action(): void;

    public function __construct()
    {
        $this->accessLevel = $this->accessLevel();
        $this->trigger = $this->trigger();
    }

    public function register(): void
    {
        Bot::get()->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
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
                $this->action();
            }
        });
    }
}
