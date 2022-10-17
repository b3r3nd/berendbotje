<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\EmbedBuilder;
use App\Models\Admin;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class Timeout
{
    public function __construct(Bot $bot)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if (!Admin::hasLevel($message->author->id, AccessLevels::MOD->value)) {
                return;
            }
            if ((str_starts_with($message->content, "{$bot->getPrefix()}timeouts"))) {
                $embed = EmbedBuilder::create($discord,
                    __('bot.timeout.title'),
                    __('bot.timeout.footer'),
                    '');
                $embed->setDescription(__('bot.timeout.count', ['count' => \App\Models\Timeout::count()]));
                foreach (\App\Models\Timeout::limit(10)->orderBy('created_at', 'desc')->get() as $timeout) {
                    $embed = $this->timeoutLength($embed, $timeout);
                }
                $message->channel->sendEmbed($embed);
                return;
            }
            if ((str_starts_with($message->content, "{$bot->getPrefix()}timeouts "))) {
                $embed = EmbedBuilder::create($discord,
                    __('bot.timeout.title'),
                    __('bot.timeout.footer'),
                    '');
                foreach ($message->mentions as $mention) {
                    $embed->setDescription(__('bot.timeout.count', ['count' => \App\Models\Timeout::where(['discord_id' => $mention->id])->count()]));
                    foreach (\App\Models\Timeout::where(['discord_id' => $mention->id])->orderBy('created_at', 'desc')->get() as $timeout) {
                        $embed = $this->timeoutLength($embed, $timeout);
                    }
                }
                $message->channel->sendEmbed($embed);
            }
        });
    }

    /**
     * TMP should use datetimes but for now its ok
     * @param $embed
     * @param $timeout
     * @return mixed
     */
    private function timeoutLength($embed, $timeout)
    {
        $length = $timeout->length;
        if ($length >= 60) {
            $length = $length / 60;
            $embed->addField(['name' => $timeout->discord_username . ' - ' . round($length) . ' hours ', 'value' => $timeout->reason]);
        } else {
            $embed->addField(['name' => $timeout->discord_username . ' - ' . round($length) . ' minutes ', 'value' => $timeout->reason]);
        }

        return $embed;
    }

}
