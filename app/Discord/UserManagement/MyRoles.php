<?php

namespace App\Discord\UserManagement;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;

class MyRoles extends SlashAndMessageCommand
{

    public function permission(): string
    {
        return "";
    }

    public function trigger(): string
    {
        return 'access';
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
