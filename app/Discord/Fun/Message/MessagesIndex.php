<?php

namespace App\Discord\Fun\Message;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Helper;
use Discord\Parts\Embed\Embed;

class MessagesIndex extends SlashAndMessageIndexCommand
{

    public function permission(): string
    {
        return "";
    }

    public function trigger(): string
    {
        return 'messages';
    }

    public function getEmbed(): Embed
    {
        $this->total = \App\Models\MessageCounter::byGuild($this->guildId)->count();

        $description = "";
        foreach (\App\Models\MessageCounter::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $index => $messageCounter) {
            $description .= Helper::indexPrefix($index, $this->offset);
            $count = $messageCounter->count * Bot::get()->getGuild($this->guildId)->getSetting('xp_count', $this->guildId);
            $description .= "**{$messageCounter->user->tag()}** • {$messageCounter->count} messages • {$count} xp \n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.messages.title'))
            ->setFooter(__('bot.messages.footer', ['xp' => Bot::get()->getGuild($this->guildId)->getSetting('xp_count', $this->guildId)]))
            ->setDescription(__('bot.messages.description', ['users' => $description]))
            ->getEmbed();
    }
}
