<?php

namespace App\Discord\Administration;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Guild;
use Discord\Builders\MessageBuilder;

class Servers extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ADMIN_SERVER;
    }

    public function trigger(): string
    {
        return 'servers';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.servers');
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $description = "";
        foreach (Guild::all() as $guild) {
            $description .= "**{$guild->name}** • {$guild->owner->tag()}\n";
        }

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.server.title'))
            ->setFooter(__('bot.server.footer'))
            ->setDescription(__('bot.server.description', ['servers' => $description]))
            ->getEmbed());
    }
}
