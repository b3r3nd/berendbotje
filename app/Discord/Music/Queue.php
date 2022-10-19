<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;

class Queue extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'queue';
    }

    public function action(): void
    {

        $description = "";
        foreach (MusicPlayer::getPlayer()->getQueue() as $song) {
            $description .= "{$song} \n";
        }

        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.music.title'))
            ->setFooter(__('bot.music.footer'))
            ->setDescription($description);

        $this->message->channel->sendEmbed($embedBuilder->getEmbed());


    }
}
