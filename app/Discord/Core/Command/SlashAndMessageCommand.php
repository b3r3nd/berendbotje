<?php

namespace App\Discord\Core\Command;

use App\Discord\Core\Bot;
use App\Discord\Core\EmbedFactory;
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
            $message->channel->sendMessage($this->action());

        });
    }
}
