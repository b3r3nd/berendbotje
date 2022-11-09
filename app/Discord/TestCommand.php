<?php

namespace App\Discord;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\Permission;

class TestCommand extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'test';
    }

    public function action(): void
    {

//        $this->message->member->addRole(995771835767607366)->done(function () {
//            $this->message->channel->sendMessage("done??");
//
//        });


        $messages = 500;

        if (isset($this->message)) {
            $parameters = explode(' ', $this->message->content);
            if (isset($parameters[1])) {
                $messages = $parameters[1];
            }
        }

        $xpCount = Bot::get()->getGuild($this->guildId)->getSetting('xp_count');
        $xp = $messages * $xpCount;
        $level = Helper::calcLevel($messages, $xpCount);

        $this->message->channel->sendMessage("Messages: {$messages}");
        $this->message->channel->sendMessage("XP: {$xp} - Count per message: {$xpCount}");
        $this->message->channel->sendMessage("Level: {$level}");
    }
}
