<?php

namespace App\Discord\Core\Interfaces;

use App\Discord\Core\Bot;
use App\Discord\Core\Guild;
use App\Domain\Discord\Channel;
use Discord\Parts\Channel\Message;

interface MessageCreateAction
{
    /**
     * @param Bot $bot
     * @param Guild $guildModel
     * @param Message $message
     * @param Channel|null $channel
     * @return void
     */
    public function execute(Bot $bot, Guild $guildModel, Message $message, ?Channel $channel): void;
}
