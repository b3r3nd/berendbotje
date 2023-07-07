<?php

namespace App\Discord\Moderation\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Moderation\Models\Channel;
use App\Discord\Roles\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
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
        $embedBuilder = EmbedBuilder::create($this, __('bot.channels.title'));

        foreach (Channel::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $channel) {
            $media = $channel->media_only ? 'On' : 'Off';
            $xp = $channel->no_xp ? 'On' : 'Off';
            $stickers = $channel->no_stickers ? 'On' : 'Off';
            $noLog = $channel->no_log ? 'On' : 'Off';
            $description = "<#{$channel->channel_id}> \n **Media only**: {$media} \n **No XP**: {$xp} \n **No Stickers**: {$stickers} \n **No Logging**: {$noLog}";
            $embedBuilder->getEmbed()->addField(
                ['name' => "", 'value' => $description, 'inline' => true],
            );
        }
        return $embedBuilder->getEmbed();
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
