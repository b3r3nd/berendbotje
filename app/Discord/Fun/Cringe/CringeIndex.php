<?php

namespace App\Discord\Fun\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Helper;
use App\Models\CringeCounter;
use Discord\Parts\Embed\Embed;

class CringeIndex extends SlashAndMessageIndexCommand
{
    public function permission(): string
    {
        return "";
    }

    public function trigger(): string
    {
        return 'cringecounter';
    }

    public function getEmbed(): Embed
    {
        $this->total = CringeCounter::byGuild($this->guildId)->count();

        $description = "";
        foreach (CringeCounter::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $index => $cringeCounter) {
            $description .= Helper::indexPrefix($index, $this->offset);
            $description .= "**{$cringeCounter->user->tag()}** • {$cringeCounter->count} \n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.cringe.title'))
            ->setFooter(__('bot.cringe.footer'))
            ->setDescription(__('bot.cringe.description', ['users' => $description]))
            ->getEmbed();
    }
}
