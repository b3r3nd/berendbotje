<?php

namespace App\Discord\MediaFilter;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\MediaChannel;
use Discord\Parts\Embed\Embed;

class MediaChannelIndex extends SlashAndMessageIndexCommand
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
        $this->total = MediaChannel::byGuild($this->guildId)->count();
        $channels = "";
        foreach (MediaChannel::byGuild($this->guildId)->skip($this->offset)->limit($this->perPage)->get() as $channel) {
            $channels .= "** {$channel->channel} **\n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.media.title'))
            ->setFooter(__('bot.media.footer'))
            ->setDescription(__('bot.media.description', ['channels' => $channels]))
            ->getEmbed();
    }
}
