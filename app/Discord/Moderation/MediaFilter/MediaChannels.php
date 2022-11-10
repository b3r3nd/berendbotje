<?php

namespace App\Discord\Moderation\MediaFilter;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashAndMessageIndexCommand;
use App\Models\MediaChannel;
use Discord\Parts\Embed\Embed;

class MediaChannels extends SlashAndMessageIndexCommand
{
    public function permission(): Permission
    {
        return Permission::MEDIA;
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
