<?php

namespace App\Discord;

use App\Models\Admin;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;

class Test
{

    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }
            if (str_starts_with($message->content, $bot->getPrefix() . 'test')) {
                $embed = new Embed($discord);
                $embed->setType('rich');
                $embed->setFooter('Usage: admins, addadmin, deladmin, clvladmin');
                $embed->setDescription('List of bot administrators');
                $embed->setTitle('Admins');


                foreach (Admin::orderBy('level', 'desc')->get() as $admin) {
                    $embed->addField(['name' => $admin->discord_username, 'value' => $admin->level]);
                }


                $message->channel->sendEmbed($embed);


            }
        });
    }

}
