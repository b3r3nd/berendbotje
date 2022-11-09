<?php

namespace App\Discord\Administration;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\Guild;

class Servers extends MessageCommand
{

    public function permission(): string
    {
        return 'servers';
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
