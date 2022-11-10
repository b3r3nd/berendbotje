<?php

namespace App\Discord\Levels;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\SlashAndMessageCommand;
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
        $xpCount = Bot::get()->getGuild($this->guildId)->getSetting(Setting::XP_COUNT);

        $voice = $messageCounter->voice_seconds / 60;
        if ($voice >= 60) {
            $voice = round($voice / 60);
            $voice = "{$voice} hours";
        } else {
            $voice = round($voice);
            $voice = "{$voice} minutes";
        }

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::getDiscord())
            ->setDescription(__('bot.xp.description', ['messages' => $messageCounter->count, 'xp' => $messageCounter->xp, 'voice' => $voice]))
            ->setTitle(__('bot.xp.title', ['level' => $messageCounter->level]))
            ->setFooter(__('bot.xp.footer', ['xp' => $xpCount]))
            ->getEmbed());
    }
}
