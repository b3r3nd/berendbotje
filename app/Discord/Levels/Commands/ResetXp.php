<?php

namespace App\Discord\Levels\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class ResetXp extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_XP;
    }

    public function trigger(): string
    {
        return 'resetxp';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.reset-xp');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $user = DiscordUser::get($this->getOption('user_mention'));
        $guild = Guild::get($this->guildId);
        $messageCounters = $user->messageCounters()->where('guild_id', $guild->id)->get();

        if ($messageCounters->isEmpty()) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.xp.not-exist', ['user' => $this->getOption('user_mention')]));
        }

        $messageCounters->first()->delete();
        return EmbedFactory::successEmbed($this->discord, __('bot.xp.reset', ['user' => $this->getOption('user_mention')]));
    }
}
