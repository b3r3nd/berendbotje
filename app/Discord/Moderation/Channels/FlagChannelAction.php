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

    public function __construct(array $arguments, string $guildId, bool $added)
    {
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
        $return = $channel->update([$this->arguments[1] => $this->added]);
        var_dump($return);
        Bot::get()->getGuild($this->guildId)->updateChannel($channel);
    }
}
