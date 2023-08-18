<?php

namespace App\Discord\Moderation\Actions;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Domain\Discord\Channel;
use App\Domain\Discord\Guild;
use Discord\Helpers\Collection;

class FlagChannelAction implements Action
{
    /**
     * @param Collection $options
     * @param string $guildId
     * @param bool $added
     * @param Bot $bot
     */
    public function __construct(
        private readonly Collection $options,
        private readonly string     $guildId,
        private readonly bool       $added,
        private readonly Bot        $bot,
    )
    {
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
