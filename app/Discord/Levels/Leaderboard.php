<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashAndMessageIndexCommand;
use App\Discord\Helper;
use Discord\Parts\Embed\Embed;

class Leaderboard extends SlashAndMessageIndexCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'leaderboard';
    }

    public function getEmbed(): Embed
    {
        $this->total = \App\Models\MessageCounter::byGuild($this->guildId)->count();

        $description = "";
        foreach (\App\Models\MessageCounter::byGuild($this->guildId)->orderBy('level', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $index => $messageCounter) {
            $description .= Helper::indexPrefix($index, $this->offset);
            $description .= "**{$messageCounter->level} • {$messageCounter->user->tag()}** • {$messageCounter->count} messages • {$messageCounter->xp} xp \n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.messages.title'))
            ->setFooter(__('bot.messages.footer', ['xp' => Bot::get()->getGuild($this->guildId)->getSetting('xp_count', $this->guildId)]))
            ->setDescription(__('bot.messages.description', ['users' => $description]))
            ->getEmbed();
    }
}
