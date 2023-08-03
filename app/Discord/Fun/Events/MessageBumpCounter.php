<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Guild;
use App\Discord\Core\Interfaces\Events\MESSAGE_CREATE;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Fun\Jobs\ProcessBumpReminder;
use App\Discord\Fun\Models\Bump;
use App\Discord\Moderation\Models\Channel;
use Discord\Discord;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;


class MessageBumpCounter extends DiscordEvent implements MESSAGE_CREATE
{
    public function event(): string
    {
        return Event::MESSAGE_CREATE;
    }

    /**
     * @param Message $message
     * @param Discord $discord
     * @return void
     * @throws NoPermissionsException
     */
    public function execute(Message $message, Discord $discord): void
    {
        if ($message->type === 20 && $message->interaction->name === 'bump') {

            $guild = $this->bot->getGuild($message->guild_id);
            if (!$guild) {
                return;
            }
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
