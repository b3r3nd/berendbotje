<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Guild;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Fun\Jobs\ProcessBumpReminder;
use App\Discord\Fun\Models\Bump;
use App\Models\Channel;
use Discord\Parts\Channel\Message;


class MessageBumpCounter implements MessageCreateAction
{
    public function execute(Bot $bot, Guild $guild, Message $message, ?Channel $channel): void
    {
        if ($message->type === 20 && $message->interaction->name === 'bump') {
            if (!$guild->getSetting(Setting::ENABLE_BUMP)) {
                return;
            }

            $user = DiscordUser::get($message->interaction->user->id);
            $bumpCounter = new Bump(['count' => 1, 'guild_id' => $guild->model->id]);
            $user->bumpCounters()->save($bumpCounter);
            $user->refresh();
            $count = $user->bumpCounters()->where('guild_id', $guild->model->id)->sum('count');
            $message->channel->sendMessage(__('bot.bump.inc', ['name' => $message->interaction->user->username, 'count' => $count ?? 0]));
            if ($guild->getSetting(Setting::ENABLE_BUMP_REMINDER)) {
                ProcessBumpReminder::dispatch($message->guild_id)->delay(now()->addHours(2));
            }
        }
    }
}
