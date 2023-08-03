<?php

namespace App\Discord\Logger\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\GUILD_BAN_REMOVE;
use App\Discord\Logger\Enums\LogSetting;
use Discord\Discord;
use Discord\Parts\Guild\Ban;
use Discord\WebSockets\Event;

class GuildBanRemove extends DiscordEvent implements GUILD_BAN_REMOVE
{

    public function event(): string
    {
        return Event::GUILD_BAN_REMOVE;
    }

    public function execute(Ban $ban, Discord $discord): void
    {
        $guild = $this->bot->getGuild($ban->guild_id);
        if ($guild->getLogSetting(LogSetting::UNBANNED_SERVER)) {
            $guild->logWithMember($ban->user, __('bot.log.joined', ['user' => $ban->user_id]), 'success');
        }
    }
}
