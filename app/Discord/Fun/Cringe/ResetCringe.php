<?php

namespace App\Discord\Fun\Cringe;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashAndMessageCommand;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class ResetCringe extends SlashAndMessageCommand
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
        $this->requiredArguments = 1;
        $this->usageString = __('bot.cringe.usage-resetcringe');
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
        $guildModel = \App\Models\Guild::get($this->guildId);
        $cringeCounters = $user->cringeCounters()->where('guild_id', $guildModel->id)->get();

        if ($cringeCounters->isEmpty()) {
            return EmbedFactory::failedEmbed(__('bot.cringe.not-cringe', ['name' => "<@{$this->arguments[0]}>"]));
        }

        $cringeCounters->first()->delete();
        return EmbedFactory::successEmbed(__('bot.cringe.reset', ['user' => $user->tag()]));
    }
}
