<?php

namespace App\Discord\Administration;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;
use App\Models\Guild;

class Servers extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::ADMIN_SERVER;
    }

    public function trigger(): string
    {
        return 'servers';
    }

    public function action(): void
    {
        $description = "";
        foreach (Guild::all() as $guild) {
            $description .= "**{$guild->name}** • {$guild->owner->tag()}\n";
        }

        $this->message->channel->sendEmbed(EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.server.title'))
            ->setFooter(__('bot.server.footer'))
            ->setDescription(__('bot.server.description', ['servers' => $description]))
            ->getEmbed());
    }
}
