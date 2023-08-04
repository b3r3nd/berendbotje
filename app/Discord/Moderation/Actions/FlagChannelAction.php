<?php

namespace App\Discord\Moderation\Actions;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Domain\Discord\Channel;
use App\Domain\Discord\Guild;
use Discord\Helpers\Collection;

/**
 * @property Collection $options  List of options from the slash command using this action.
 * @property string $guildId            Discord id of the guild.
 * @property bool $added                If the channel flag is being added or removed.
 * @property Bot $bot                   Main bot instance.
 */
class FlagChannelAction implements Action
{
    private Collection $options;
    private string $guildId;
    private bool $added;
    private Bot $bot;

    /**
     * @param Collection $options
     * @param string $guildId
     * @param bool $added
     * @param Bot $bot
     */
    public function __construct(Collection $options, string $guildId, bool $added, Bot $bot)
    {
        $this->bot = $bot;
        $this->guildId = $guildId;
        $this->options = $options;
        $this->added = $added;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $channel = $this->options->first()->options->get('name', 'channel')->value;
        $channelModel = Channel::get($channel, $this->guildId);
        if (!$channelModel) {
            $channelModel = Channel::create(['channel_id' => $channel, 'guild_id' => Guild::get($this->guildId)->id]);
        }
        $channelModel->update([$this->options->first()->options->get('name', 'flag')->value => $this->added]);
        $this->bot->getGuild($this->guildId)?->updateChannel($channelModel);
    }
}
