<?php

namespace App\Discord\Events;

use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Enums\Setting;
use App\Jobs\ProcessBumpReminder;
use App\Models\Bumper;
use App\Models\DiscordUser;
use App\Models\Guild;
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

                $bumpCounter = new Bumper(['count' => 1, 'guild_id' => $guild->id]);
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
