<?php

namespace App\Discord\Fun\Message;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\EmbedFactory;
use App\Models\DiscordUser;
use App\Models\Guild;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;

class UserMessages extends SlashAndMessageCommand
{

    public function permission(): string
    {
        return "";
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
        $user = DiscordUser::get($this->commandUser);
        $guild = Guild::get($this->guildId);

        $messageCounters = $user->messageCounters()->where('guild_id', $guild->id)->get();

        if ($messageCounters->isEmpty()) {
            return EmbedFactory::failedEmbed(__('bot.xp.not-found'));
        }

        $messages = $messageCounters->first()->count;
        $xpCount = Bot::get()->getGuild($this->guildId)->getSetting('xp_count');
        $xp = $messages * $xpCount;

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::getDiscord())
            ->setDescription(__('bot.xp.description', ['messages' => $messages, 'xp' => $xp]))
            ->setTitle(__('bot.xp.title'))
            ->setFooter(__('bot.xp.footer', ['xp' => $xpCount]))
            ->getEmbed());
    }
}
