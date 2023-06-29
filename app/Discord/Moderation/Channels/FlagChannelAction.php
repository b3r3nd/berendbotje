<?php

namespace App\Discord\Moderation\Channels;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\Action;
use App\Models\Channel;
use App\Models\Guild;

class FlagChannelAction implements Action
{
    private array $arguments;
    private string $guildId;
    private bool $added;
    private Bot $bot;

    public function __construct(array $arguments, string $guildId, bool $added, Bot $bot)
    {
        $this->bot = $bot;
        $this->guildId = $guildId;
        $this->arguments = $arguments;
        $this->added = $added;
    }

    public function execute(): void
    {
        $channel = Channel::get($this->arguments[0], $this->guildId);
        if (!$channel) {
            $channel = Channel::create(['channel_id' => $this->arguments[0], 'guild_id' => Guild::get($this->guildId)->id]);
        }
        $channel->update([$this->arguments[1] => $this->added]);
        $this->bot->getGuild($this->guildId)?->updateChannel($channel);
    }
}
