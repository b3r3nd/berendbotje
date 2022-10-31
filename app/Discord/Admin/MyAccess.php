<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;

class MyAccess extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'access';
    }

    public function action(): MessageBuilder
    {
        $level = DiscordUser::where('discord_id', $this->commandUser)->first()->admin->level ?? 0;

        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.access.title'))
            ->setFooter(__('bot.access.footer'));

        if ($level === 0) {
            $embedBuilder->setFailed();
            $embedBuilder->setDescription(__('bot.access.desc-lack'));
        } else {
            $embedBuilder->setDescription(__('bot.access.desc', ['level' => $level]));
        }

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
