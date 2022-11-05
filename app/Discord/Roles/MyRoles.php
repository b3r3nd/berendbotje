<?php

namespace App\Discord\Roles;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;

class MyRoles extends MessageCommand
{

    public function permission(): string
    {
        return "";
    }

    public function trigger(): string
    {
        return 'myroles';
    }

    public function action(): void
    {
        $description = "";
        if (DiscordUser::get($this->commandUser)->roles->isEmpty()) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.myroles.none')));
            return;
        }
        foreach (DiscordUser::get($this->commandUser)->rolesByGuild($this->guildId) as $role) {
            $description .= "{$role->name}\n";
        }

        $this->message->channel->sendMessage(MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.myroles.title'))
            ->setFooter(__('bot.myroles.footer'))
            ->setDescription(__('bot.myroles.description', ['roles' => $description]))
            ->getEmbed()));
    }
}
