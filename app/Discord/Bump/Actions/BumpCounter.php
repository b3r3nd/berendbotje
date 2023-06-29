<?php

namespace App\Discord\Bump\Actions;

use App\Discord\Bump\Jobs\ProcessBumpReminder;
use App\Discord\Bump\Models\Bump;
use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use App\Discord\Settings\Enums\Setting;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;


class BumpCounter extends DiscordEvent
{
    public function registerEvent(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->type === 20 && $message->interaction->name === 'bump') {

                if (!$this->bot->getGuild($message->guild_id)?->getSetting(Setting::ENABLE_BUMP)) {
                    return;
                }
                $guild = Guild::get($message->guild_id);
                $user = DiscordUser::get($message->interaction->user->id);

                $bumpCounter = new Bump(['count' => 1, 'guild_id' => $guild->id]);
                $user->bumpCounters()->save($bumpCounter);

                $user->refresh();

                $count = $user->bumpCounters()->where('guild_id', $guild->id)->sum('count');
                $message->channel->sendMessage(__('bot.bump.inc', ['name' => $message->interaction->user->username, 'count' => $count ?? 0]));

                if ($this->bot->getGuild($message->guild_id)?->getSetting(Setting::ENABLE_BUMP_REMINDER)) {
                    ProcessBumpReminder::dispatch($message->guild_id)->delay(now()->addHours(2));
                }
            }
        });
    }
}
