<?php

namespace App\Discord\Music;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\MessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Song;

class Queue extends MessageCommand
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
        foreach (Song::all() as $song) {
            $description .= "** {$song->id}. **{$song->youtube_url} \n";
        }

        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.music.title'))
            ->setFooter(__('bot.music.footer'))
            ->setDescription($description);

        $this->message->channel->sendEmbed($embedBuilder->getEmbed());


    }
}
