<?php

namespace App\Discord;

use App\Models\Admin;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class AdminManagement
{

    public function __construct(Discord $discord, Bot $bot)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($bot) {
            if ($message->author->bot) {
                return;
            }

            if (!Admin::isAdmin($message->author->id)) {
                return;
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'admins')) {
                foreach (Admin::all() as $admin) {
                    $message->channel->sendMessage($admin->discord_username . ' -> ' . $admin->level);
                }
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

                    $admin->update(['level' => $parameters[2]]);
                    $message->channel->sendMessage("User level changed");
                }
            }


        });
    }
}
