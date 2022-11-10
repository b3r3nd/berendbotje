<?php

namespace App\Discord\Fun\Bump;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashAndMessageIndexCommand;
use App\Discord\Helper;
use App\Models\Bumper;
use Discord\Parts\Embed\Embed;

class BumpStatistics extends SlashAndMessageIndexCommand
{
    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'bumpstats';
    }


    public function getEmbed(): Embed
    {
        $this->total = Bumper::byGuild($this->guildId)->count();
        $description = "";
        foreach (Bumper::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $index => $bumper) {
            $description .= Helper::indexPrefix($index);
            $description .= "**{$bumper->user->tag()}** •  {$bumper->count}\n";
        }
        return EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.bump.title'))
            ->setFooter(__('bot.bump.footer'))
            ->setDescription(__('bot.bump.description', ['bumpers' => $description]))
            ->getEmbed();

    }
}
