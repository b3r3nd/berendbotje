<?php

namespace App\Discord;

use App\Models\Bumper;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;

class Timeout
{
    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }

            $embed = new Embed($discord);
            $embed->setType('rich');
            $embed->setFooter('Timeouts given through discord are automatically added');
            $embed->setTitle('Timeouts');
            $embed->setColor(15548997);

            if ((str_starts_with($message->content, $bot->getPrefix() . 'timeouts '))) {

                foreach ($message->mentions as $mention) {
                    $count = \App\Models\Timeout::where(['discord_id' => $mention->id])->count();
                    $embed->setDescription('Total timeouts: ' . $count);

                    foreach (\App\Models\Timeout::where(['discord_id' => $mention->id])->orderBy('created_at', 'desc')->get() as $timeout) {
                        $embed = $this->timeoutLength($embed, $timeout);
                    }
                }

                $message->channel->sendEmbed($embed);
                return;
            }

            if ((str_starts_with($message->content, $bot->getPrefix() . 'timeouts'))) {
                $embed->setDescription('Total timeouts: ' . \App\Models\Timeout::count());
                foreach (\App\Models\Timeout::limit(10)->orderBy('created_at', 'desc')->get() as $timeout) {
                    $embed = $this->timeoutLength($embed, $timeout);
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
