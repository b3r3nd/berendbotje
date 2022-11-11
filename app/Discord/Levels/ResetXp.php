<?php

namespace App\Discord\Levels;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\DiscordUser;
use App\Models\Guild;
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
        $user = DiscordUser::get($this->arguments[0]);
        $guild = Guild::get($this->guildId);
        $messageCounters = $user->messageCounters()->where('guild_id', $guild->id)->get();

        if ($messageCounters->isEmpty()) {
            return EmbedFactory::failedEmbed(__('bot.xp.not-exist', ['user' => $this->arguments[0]]));
        }

        $messageCounters->first()->delete();
        return EmbedFactory::successEmbed(__('bot.xp.reset', ['user' => $this->arguments[0]]));
    }
}
