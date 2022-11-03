<?php

namespace App\Discord\Statistics;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\ButtonFactory;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Helper;
use App\Models\Emote;
use Discord\Parts\Embed\Embed;

class EmoteIndex extends SlashAndMessageIndexCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'emotes';
    }

    public function getEmbed(): Embed
    {
        $this->total = Emote::byGuild($this->guildId)->count();
        $description = "";
        foreach (Emote::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $index => $emote) {
            $description .= Helper::indexPrefix($index, $this->offset);
            $description .= "**{$emote->emote}** • {$emote->count} \n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.emotes.title'))
            ->setFooter(__('bot.emotes.footer'))
            ->setDescription(__('bot.emotes.description', ['emotes' => $description]))
            ->getEmbed();
    }

}
