<?php

namespace App\Discord\Fun;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Models\DiscordUser;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MentionResponder
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            if ($message->mentions->count() > 0) {
                if (str_contains($message->content, $discord->user->id)) {
                    if (str_contains($message->content, '?give')) {
                        $message->reply('Thanks! 😎');
                    } else if (str_contains($message->content, Bot::get()->getPrefix() . 'addcringe')) {
                        $message->reply('Keep on dreaming.. 🖕');
                    } elseif (DiscordUser::hasLevel($message->author->id, $message->guild_id, AccessLevels::GOD->value)) {
                        $message->reply('Yes? 😎');
                    } else {
                        $options = [
                            'Need something..?',
                            'Uh, excuse me?',
                            '...',
                            '🤔',
                            '🖕',
                        ];

                        $message->reply($options[rand(0, (count($options) - 1))]);
                    }
                }
            }
        });
    }
}
