<?php

namespace App\Discord\Fun\Cringe;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class ResetCringe extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::DEL_CRINGE;
    }

    public function trigger(): string
    {
        return 'resetcringe';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.reset-cringe');
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
        $guildModel = \App\Models\Guild::get($this->guildId);
        $cringeCounters = $user->cringeCounters()->where('guild_id', $guildModel->id)->get();

        if ($cringeCounters->isEmpty()) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.cringe.not-cringe', ['name' => "<@{$this->getOption('user_mention')}>"]));
        }

        $cringeCounters->first()->delete();
        return EmbedFactory::successEmbed($this->discord, __('bot.cringe.reset', ['user' => $user->tag()]));
    }
}
