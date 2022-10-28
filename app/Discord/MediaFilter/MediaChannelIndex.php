<?php

namespace App\Discord\MediaFilter;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Models\MediaChannel;
use Discord\Parts\Embed\Embed;

class MediaChannelIndex extends SlashIndexCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'mediachannels';
    }

    public function getEmbed(): Embed
    {
        $this->total = MediaChannel::count();
        $channels = "";
        foreach (MediaChannel::skip($this->offset)->limit($this->perPage)->get() as $channel) {
            $channels .= "** {$channel->channel} **\n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.media.title'))
            ->setFooter(__('bot.media.footer'))
            ->setDescription(__('bot.media.description', ['channels' => $channels]))
            ->getEmbed();
    }
}
