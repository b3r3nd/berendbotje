<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Discord\Helper;
use App\Models\DiscordUser;
use App\Models\Guild;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;

class UserRank extends SlashAndMessageCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'rank';
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

        $messageCounter = $messageCounters->first();
        $xpCount = Bot::get()->getGuild($this->guildId)->getSetting('xp_count');

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::getDiscord())
            ->setDescription(__('bot.xp.description', ['messages' => $messageCounter->count, 'xp' => $messageCounter->xp]))
            ->setTitle(__('bot.xp.title', ['level' => $messageCounter->level]))
            ->setFooter(__('bot.xp.footer', ['xp' => $xpCount]))
            ->getEmbed());
    }
}
