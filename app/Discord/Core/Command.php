<?php

namespace App\Discord\Core;

use App\Models\Admin;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

/**
 * All commands are single classes. They will all extend this class! It will handle some basic filters so the
 * child only has to worry about the actual custom code required for the command instead of rewriting the same
 * 20 lines of code each time.
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
                if (!Admin::hasLevel($message->author->id, $this->accessLevel->value)) {
                    $message->channel->sendMessage(__("bot.lack-access"));
                    return;
                }

                if ($this->requiresMention) {
                    if ($message->mentions->count() == 0) {
                        $message->channel->sendMessage(__('bot.provide-mention'));
                        return;
                    }
                }

                if ($this->requiredArguments > 0) {
                    $parameters = explode(' ', $message->content);
                    if (!isset($parameters[$this->requiredArguments])) {
                        $message->channel->sendMessage(__('bot.provide-arguments', ['count' => $this->requiredArguments]));
                        return;
                    } else {
                        array_shift($parameters);
                        $this->arguments = $parameters;
                        $this->messageString = join(' ', $this->arguments);
                    }
                }

                $this->message = $message;
                $this->action();
            }
        });
    }
}
