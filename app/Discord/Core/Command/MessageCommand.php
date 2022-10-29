<?php

namespace App\Discord\Core\Command;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

/**
 * Extendable class to easily create new message only commands. For better understanding:
 * @see Command
 * @see MessageCommandTrait
 */
abstract class MessageCommand extends Command
{
    use MessageCommandTrait;

    public abstract function action(): void;

    /**
     * I would love to move this code to the trait like I did with slash commands, but depending on where the data is going
     * (message only command, or both slash and message command) something else needs to be done.
     *
     * @return void
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
                $this->validateMessage($message);
                $this->message = $message;
                $this->action();
            }
        });
    }
}