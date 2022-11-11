<?php

namespace App\Discord\Roles;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;

class MyRoles extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'myroles';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.myroles');
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $description = "";
        if (DiscordUser::get($this->commandUser)->roles->isEmpty()) {
            return EmbedFactory::failedEmbed(__('bot.myroles.none'));
        }
        foreach (DiscordUser::get($this->commandUser)->rolesByGuild($this->guildId) as $role) {
            $description .= "{$role->name}\n";
        }

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.myroles.title'))
            ->setFooter(__('bot.myroles.footer'))
            ->setDescription(__('bot.myroles.description', ['roles' => $description]))
            ->getEmbed());
    }
}
