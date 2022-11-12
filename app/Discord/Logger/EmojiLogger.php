<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use Discord\Discord;
use Discord\Helpers\Collection;
use Discord\WebSockets\Event;

class EmojiLogger
{

    public function __construct()
    {
        Bot::getDiscord()->on(Event::GUILD_EMOJIS_UPDATE, function (Collection $emojis, Discord $discord, Collection $oldEmojis) {
           // no guild id?
        });
    }
}
