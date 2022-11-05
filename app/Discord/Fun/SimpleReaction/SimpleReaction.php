<?php

namespace App\Discord\Fun\SimpleReaction;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class SimpleReaction
{
    public static function create(Bot $bot, string $trigger, string $reaction, string $guildId)
    {
        new self($bot, $trigger, $reaction, $guildId);
    }

    public function __construct(Bot $bot, string $trigger, string $reaction, string $guildId)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($trigger, $reaction, $bot, $guildId) {
            if ($message->author->bot) {
                return;
            }
            if ($message->guild_id != $guildId) {
                return;
            }
            if (str_contains(strtolower($message->content), strtolower($trigger))) {
                if (!in_array($trigger, $bot->getGuild($guildId)->getDeletedReactions())) {

                    if (str_contains($reaction, "<")) {
                        $reaction = str_replace(["<", ">"], "", $reaction);
                    }

                    $message->react($reaction);
                }
            }
        });
    }
}
