<?php

namespace App\Discord\Fun\Message;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageIndexCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\Permission;
use App\Models\RoleReward;
use Discord\Parts\Embed\Embed;

class RoleRewards extends SlashAndMessageIndexCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'rewards';
    }

    public function getEmbed(): Embed
    {
        $this->total = RoleReward::byGuild($this->guildId)->count();

        $description = "";
        foreach (RoleReward::byGuild($this->guildId)->orderBy('level', 'desc')->skip($this->offset)->limit($this->perPage)->get() as $index => $roleReward) {
            $description .= "**Level {$roleReward->level}** • {$roleReward->roleTag()} \n";
        }
        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.rewards.title'))
            ->setFooter(__('bot.rewards.footer'))
            ->setDescription(__('bot.rewards.description', ['rewards' => $description]))
            ->getEmbed();
    }
}
