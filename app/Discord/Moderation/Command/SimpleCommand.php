<?php

namespace App\Discord\Moderation\Command;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleCommand
{
    public static function create(Bot $bot, string $trigger, string $response, string $guildId)
    {
        new self($bot, $trigger, $response, $guildId);
    }

    public function __construct(Bot $bot, string $trigger, string $response, string $guildId)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($trigger, $response, $bot, $guildId) {
            if ($message->guild_id != $guildId) {
                return;
            }

            if ($message->author->bot) {
                return;
            }
            if (strtolower($message->content) == strtolower($trigger)) {
                if (!in_array(strtolower($message->content), $bot->getGuild($guildId)->getDeletedCommands())) {
                    $message->channel->sendMessage($response);
                }
            }
        });
    }
}
