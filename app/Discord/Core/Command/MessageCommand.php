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
            $this->action();
        });
    }
}
