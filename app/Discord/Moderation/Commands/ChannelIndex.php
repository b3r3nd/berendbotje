<?php

namespace App\Discord\Moderation\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Moderation\Models\Channel;
use App\Discord\Roles\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Exception;

class ChannelIndex extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::CHANNEL;
    }

    public function trigger(): string
    {
        return 'channels';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.channels');
        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->total = Channel::byGuild($this->guildId)->count();
        $channels = "";
        foreach (Channel::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $channel) {
            $media = $channel->media_only ? 'On' : 'Off';
            $xp = $channel->no_xp ? 'On' : 'Off';
            $stickers = $channel->no_stickers ? 'On' : 'Off';
            $noLog = $channel->no_log ? 'On' : 'Off';
            $channels .= "** <#{$channel->channel_id}> **
            **Media only**: {$media}
            **No XP**: {$xp}
            **No Stickers**: {$stickers}
             **No Logging**: {$noLog}
            \n";
        }
        return EmbedBuilder::create($this, __('bot.channels.title'), __('bot.channels.description', ['channels' => $channels]))->getEmbed();
    }
}
