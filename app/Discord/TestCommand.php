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
