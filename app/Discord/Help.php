<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedBuilder;

class Help extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'help';
    }

    public function action(): void
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.help.title'))
            ->setFooter(__('bot.help.footer'));
        $embedBuilder->getEmbed()->addField(
            ['name' => 'Prefix', 'value' => 'All commands use $ prefix!'],
            ['name' => 'Admins', 'value' => 'addadmin, deladmin, admins'],
            ['name' => 'Music Player', 'value' => 'addsong, play, stop, pause, resume'],
            ['name' => 'Cringe Counter', 'value' => 'addcringe, delcringe, cringecounter'],
            ['name' => 'Timeouts', 'value' => 'timeouts, usertimouts'],
            ['name' => 'Bumper Elite', 'value' => 'bumpstats'],
            ['name' => 'Simple Reactions', 'value' => 'reactions, addreaction, delreaction'],
            ['name' => 'Simple Commands', 'value' => 'commands, addcmd, delcmd'],
            ['name' => 'Source Code', 'value' => 'https://gitlab.com/discord54/berend-botje/'],
        );
        $this->message->channel->sendEmbed($embedBuilder->getEmbed());
    }
}
