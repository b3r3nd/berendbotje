<?php

namespace App\Discord\Moderation\Channels;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Models\Channel;
use App\Models\Guild;
use Discord\Repository\Interaction\OptionRepository;

class FlagChannelAction implements Action
{
    private OptionRepository $options;
    private string $guildId;
    private bool $added;
    private Bot $bot;

    public function __construct(OptionRepository $options, string $guildId, bool $added, Bot $bot)
    {
        $this->bot = $bot;
        $this->guildId = $guildId;
        $this->options = $options;
        $this->added = $added;
    }

    public function execute(): void
    {
        $channel = Channel::get($this->options->get('name', 'channel')->value, $this->guildId);
        if (!$channel) {
            $channel = Channel::create(['channel_id' => $this->options->get('name', 'channel')->value, 'guild_id' => Guild::get($this->guildId)->id]);
        }
        $channel->update([$this->options->get('name', 'flag')->value => $this->added]);
        $this->bot->getGuild($this->guildId)?->updateChannel($channel);
    }
}
