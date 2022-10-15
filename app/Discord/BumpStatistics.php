<?php

namespace App\Discord;

use App\Models\Admin;
use App\Models\Bumper;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;

class BumpStatistics
{
    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if ((str_starts_with($message->content, $bot->getPrefix() . 'bumpstats'))) {

                $embed = new Embed($discord);
                $embed->setType('rich');
                $embed->setFooter('Use /bump in #botspam');
                $embed->setDescription('Bump counter');
                $embed->setTitle('Bumper Elites');

                foreach (Bumper::orderBy('count', 'desc')->limit(10)->get() as $bumper) {
                    $embed->addField(['name' => $bumper->discord_username, 'value' => $bumper->count, 'inline' => true]);
                }

                $message->channel->sendEmbed($embed);
            }
        });
    }

}
