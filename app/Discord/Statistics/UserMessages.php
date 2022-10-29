<?php

namespace App\Discord\Statistics;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;

class UserMessages extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'xp';
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): MessageBuilder
    {
        $user = DiscordUser::where('discord_id', $this->commandUser)->first();

        if (!$user) {
            return EmbedFactory::failedEmbed(__('bot.xp.not-found'));
        }
        $messages = $user->messageCounter->count;
        $xpCount = Bot::get()->getSetting('xp_count');
        $xp = $messages * $xpCount;

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::getDiscord())
            ->setDescription(__('bot.xp.description', ['messages' => $messages, 'xp' => $xp]))
            ->setTitle(__('bot.xp.title'))
            ->setFooter(__('bot.xp.footer', ['xp' => $xpCount]))
            ->getEmbed());
    }
}
