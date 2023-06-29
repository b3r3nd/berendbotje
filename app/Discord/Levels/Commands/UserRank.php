<?php

namespace App\Discord\Levels\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use App\Discord\Settings\Enums\Setting;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class UserRank extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'rank';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.rank');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => false,
            ],
        ];

        parent::__construct();
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): MessageBuilder
    {

        if ($this->getOption('user_mention')) {
            $user = DiscordUser::get($this->getOption('user_mention'));
        } else {
            $user = DiscordUser::get($this->commandUser);
        }

        $guild = Guild::get($this->guildId);

        $messageCounters = $user->messageCounters()->where('guild_id', $guild->id)->get();

        if ($messageCounters->isEmpty()) {
            return EmbedFactory::failedEmbed($this->discord, __('bot.xp.not-found', ['user' => $user->tag()]));
        }

        $messageCounter = $messageCounters->first();
        $xpCount = $this->bot->getGuild($this->guildId)?->getSetting(Setting::XP_COUNT);

        $voice = $messageCounter->voice_seconds / 60;
        if ($voice >= 60) {
            $voice = round($voice / 60);
            $voice = "{$voice} hours";
        } else {
            $voice = round($voice);
            $voice = "{$voice} minutes";
        }

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create($this->bot->discord)
            ->setDescription(__('bot.xp.description', ['user' => $user->tag(), 'messages' => $messageCounter->count, 'xp' => $messageCounter->xp, 'voice' => $voice, 'level' => $messageCounter->level]))
            ->setTitle(__('bot.xp.title', ['level' => $messageCounter->level, 'xp' => $messageCounter->xp]))
            ->setFooter(__('bot.xp.footer', ['xp' => $xpCount]))
            ->getEmbed());
    }
}
