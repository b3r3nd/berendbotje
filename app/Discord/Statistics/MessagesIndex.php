<?php

namespace App\Discord\Statistics;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Helper;
use Discord\Parts\Embed\Embed;

class MessagesIndex extends SlashAndMessageIndexCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'messages';
    }

    public function getEmbed(): Embed
    {
        $this->total = \App\Models\MessageCounter::count();

        $description = "";
        foreach (\App\Models\MessageCounter::orderBy('count', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $index => $messageCounter) {
            $description .= Helper::indexPrefix($index, $this->offset);
            $count = $messageCounter->count * Bot::get()->getSetting('xp_count');
            $description .= "**{$messageCounter->user->discord_tag}** • {$messageCounter->count} messages • {$count} xp \n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.messages.title'))
            ->setFooter(__('bot.messages.footer', ['xp' => Bot::get()->getSetting('xp_count')]))
            ->setDescription(__('bot.messages.description', ['users' => $description]))
            ->getEmbed();
    }
}
