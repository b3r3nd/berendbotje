<?php

namespace App\Discord;

use App\Discord\Core\EmbedBuilder;
use App\Models\Bumper;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class BumpStatistics
{
    public function __construct(Bot $bot)
    {
        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if ((str_starts_with($message->content, $bot->getPrefix() . 'bumpstats'))) {
                $description = "";
                foreach (Bumper::orderBy('count', 'desc')->limit(10)->get() as $bumper) {
                    $description .= "**{$bumper->discord_username}** - {$bumper->count}\n";
                }
                $embed = EmbedBuilder::create($discord,
                    __('bot.bump.title'),
                    __('bot.bump.footer'),
                    __('bot.bump.description', ['bumpers' => $description]));

                $message->channel->sendEmbed($embed);
            }
        });
    }

}
