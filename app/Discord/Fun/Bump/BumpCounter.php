<?php

namespace App\Discord\Fun\Bump;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Jobs\ProcessBumpReminder;
use App\Models\Bumper;
use App\Models\DiscordUser;
use App\Models\Guild;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;


class BumpCounter
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->type === 20 && $message->interaction->name === '8ball') {

                if (!Bot::get()->getGuild($message->guild_id)?->getSetting(Setting::ENABLE_BUMP)) {
                    return;
                }
                $guild = Guild::get($message->guild_id);
                $user = DiscordUser::get($message->interaction->user->id);

                $bumpCounter = new Bumper(['count' => 1, 'guild_id' => $guild->id]);
                $user->bumpCounters()->save($bumpCounter);

                $user->refresh();

                $count = $user->bumpCounters()->where('guild_id', $guild->id)->sum('count');
                $message->channel->sendMessage(__('bot.bump.inc', ['name' => $message->interaction->user->username, 'count' => $count ?? 0]));

                if (Bot::get()->getGuild($message->guild_id)?->getSetting(Setting::ENABLE_BUMP_REMINDER)) {
                    ProcessBumpReminder::dispatch($message->guild_id)->delay(now()->addMinute());
                }
            }
        });
    }
}
