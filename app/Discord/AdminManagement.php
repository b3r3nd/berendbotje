<?php

namespace App\Discord;

use App\Discord\Core\EmbedBuilder;
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
                $description = "";
                foreach (Admin::orderBy('level', 'desc')->get() as $admin) {
                    $description .= '**' . $admin->discord_username . '** - ' . $admin->level . "\n";
                }
                $embed = EmbedBuilder::create($discord,
                    __('bot.admins.title'),
                    __('bot.admins.footer'),
                    __('bot.admins.description', ['admins' => $description]));

                $message->channel->sendEmbed($embed);
            }

            if (str_starts_with($message->content, $bot->getPrefix() . 'addadmin ')) {
                foreach ($message->mentions as $mention) {
                    $admin = Admin::where(['discord_id' => $mention->id])->first();

                    if ($admin) {
                        $message->channel->sendMessage(__('bot.admins.exists'));
                        return;
                    }

                    $parameters = explode(' ', $message->content);
                    if (!isset($parameters[2])) {
                        $message->channel->sendMessage(__('bot.admins.provide-access'));
                        return;
                    }

                    if (!Admin::hasHigherLevel($message->author->id, $parameters[2])) {
                        $message->channel->sendMessage(__('bot.admins.lack-access'));
                        return;
                    }

                    Admin::create([
                        'discord_id' => $mention->id,
                        'discord_username' => $mention->username,
                        'level' => $parameters[2]
                    ]);
                    $message->channel->sendMessage(__('bot.admins.added'));
                }
            }
            if (str_starts_with($message->content, $bot->getPrefix() . 'deladmin ')) {

                foreach ($message->mentions as $mention) {
                    $admin = Admin::where(['discord_id' => $mention->id])->first();
                    if (!$admin) {
                        $message->channel->sendMessage(__('bot.admins.not-exist'));
                        return;
                    }

                    if (!Admin::hasHigherLevel($message->author->id, $admin->level)) {
                        $message->channel->sendMessage(__('bot.admins.powerful', ['name' => $admin->discord_username]));
                        return;
                    }

                    $admin->delete();
                    $message->channel->sendMessage(__('bot.admins.deleted'));
                }
            }
            if (str_starts_with($message->content, $bot->getPrefix() . 'clvladmin ')) {
                foreach ($message->mentions as $mention) {
                    $admin = Admin::where(['discord_id' => $mention->id])->first();
                    if (!$admin) {
                        $message->channel->sendMessage(__('bot.admins.not-exist'));
                        return;
                    }
                    $parameters = explode(' ', $message->content);
                    if (!isset($parameters[2])) {
                        $message->channel->sendMessage(__('bot.admins.provide-access'));
                        return;
                    }
                    if (!Admin::hasHigherLevel($message->author->id, $admin->level)) {
                        $message->channel->sendMessage(__('bot.admins.powerful', ['name' => $admin->discord_username]));
                        return;
                    }

                    if (!Admin::hasHigherLevel($message->author->id, $parameters[2])) {
                        $message->channel->sendMessage(__('bot.admins.lack-access'));
                        return;
                    }

                    $admin->update(['level' => $parameters[2]]);
                    $message->channel->sendMessage(__('bot.admins.changed'));
                }
            }
        });
    }
}
