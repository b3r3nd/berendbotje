<?php

namespace App\Discord\Timeout;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Timeout;

class AllTimeouts extends MessageCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'timeouts';
    }

    public function action(): void
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.timeout.title'))
            ->setFooter(__('bot.timeout.footer'));

        $embed = $embedBuilder->getEmbed();
        $embed->setDescription(__('bot.timeout.count', ['count' => Timeout::count()]));
        foreach (Timeout::limit(10)->orderBy('created_at', 'desc')->get() as $timeout) {
            $embed = TimeoutHelper::timeoutLength($embed, $timeout);
        }
        $this->message->channel->sendEmbed($embed);
    }
}
