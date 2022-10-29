<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use Discord\Builders\MessageBuilder;

class Help extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'help';
    }

    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.help.title'))
            ->setFooter(__('bot.help.footer'));
        $embedBuilder->getEmbed()->addField(
            ['name' => 'Prefix', 'value' => 'All commands use `$` prefix, alternatively you can use slash commands `/`.'],
            ['name' => 'Admins', 'value' => 'addadmin, deladmin, admins'],
            ['name' => 'Music Player', 'value' => 'addsong, removesong, play, stop, queue, pause, resume'],
            ['name' => 'Cringe Counter', 'value' => 'addcringe, delcringe, cringecounter'],
            ['name' => 'Timeouts', 'value' => 'timeouts, usertimouts'],
            ['name' => 'Bumper Elite', 'value' => 'bumpstats'],
            ['name' => 'Simple Reactions', 'value' => 'reactions, addreaction, delreaction'],
            ['name' => 'Simple Commands', 'value' => 'commands, addcmd, delcmd'],
            ['name' => 'Media channels', 'value' => 'mediachannels, addmediachannel, delmediachannel'],
            ['name' => 'Emote Counter', 'value' => 'emotes'],
            ['name' => 'Source Code', 'value' => 'https://gitlab.com/discord54/berend-botje/'],
        );

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
