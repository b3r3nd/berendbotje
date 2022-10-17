<?php

namespace App\Discord;

use App\Models\Admin;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;

class AdminManagement
{

    public function __construct(Bot $bot)
    {

        $bot->discord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }

            if (!Admin::hasLevel($message->author->id, AccessLevels::GOD->value)) {
                return;
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'admins')) {
                $embed = new Embed($discord);
                $embed->setType('rich');
                $embed->setFooter('Usage: admins, addadmin, deladmin, clvladmin');
                $embed->setTitle('Admins');
                $embed->setColor(2067276);
                $description = "List of bot administrators\n\n";

                foreach (Admin::orderBy('level', 'desc')->get() as $admin) {
                    $description .= '**' . $admin->discord_username . '** - ' .  $admin->level . "\n";
                }

                $embed->setDescription($description);
                $message->channel->sendEmbed($embed);
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'addadmin ')) {
                foreach ($message->mentions as $mention) {
                    $admin = Admin::where(['discord_id' => $mention->id])->first();

                    if ($admin) {
                        $message->channel->sendMessage("User already exists, you can change level with clvladmin");
                        return;
                    }

                    $parameters = explode(' ', $message->content);
                    if (!isset($parameters[2])) {
                        $message->channel->sendMessage("Provide access level..");
                        return;
                    }

                    if(!Admin::hasHigherLevel($message->author->id, $parameters[2])) {
                        $message->channel->sendMessage("Can't give more access than you have yourself..");
                        return;
                    }

                    Admin::create([
                        'discord_id' => $mention->id,
                        'discord_username' => $mention->username,
                        'level' => $parameters[2]
                    ]);
                    $message->channel->sendMessage("User added");
                }
            }
            if (str_starts_with($message->content, $bot->getPrefix() . 'deladmin ')) {

                foreach ($message->mentions as $mention) {
                    $admin = Admin::where(['discord_id' => $mention->id])->first();
                    if (!$admin) {
                        $message->channel->sendMessage("User does not exist");
                        return;
                    }

                    if(!Admin::hasHigherLevel($message->author->id, $admin->level)) {
                        $message->channel->sendMessage($admin->discord_username . ' is to powerfull for you.');
                        return;
                    }

                    $admin->delete();
                    $message->channel->sendMessage("User deleted");
                }
            }
            if (str_starts_with($message->content, $bot->getPrefix() . 'clvladmin ')) {
                foreach ($message->mentions as $mention) {
                    $admin = Admin::where(['discord_id' => $mention->id])->first();
                    if (!$admin) {
                        $message->channel->sendMessage("User does not exist");
                        return;
                    }
                    $parameters = explode(' ', $message->content);
                    if (!isset($parameters[2])) {
                        $message->channel->sendMessage("Provide access level..");
                        return;
                    }
                    if(!Admin::hasHigherLevel($message->author->id, $admin->level)) {
                        $message->channel->sendMessage($admin->discord_username . ' is to powerfull for you.');
                        return;
                    }

                    $admin->update(['level' => $parameters[2]]);
                    $message->channel->sendMessage("User level changed");
                }
            }
        });
    }
}
