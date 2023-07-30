<?php

namespace App\Discord\Core\Interfaces;

use App\Discord\Core\Bot;
use App\Discord\Core\Guild;
use App\Models\Channel;
use Discord\Parts\Channel\Message;

interface MessageCreateAction
{
    /**
     * @param Bot $bot
     * @param Guild $guild
     * @param Message $message
     * @param Channel|null $channel
     * @return void
     */
    public function execute(Bot $bot, Guild $guild, Message $message, ?Channel $channel): void;
}
