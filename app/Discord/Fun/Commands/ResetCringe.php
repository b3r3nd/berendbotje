<?php

namespace App\Discord\Fun\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

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
                'description' => __('bot.user-mention'),
                'type' => Option::USER,
                'required' => true,
            ],
        ];

        parent::__construct();
    }

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $user = DiscordUser::get($this->getOption('user_mention'));
        $guildModel = \App\Discord\Core\Models\Guild::get($this->guildId);
        $cringeCounters = $user->cringeCounters()->where('guild_id', $guildModel->id)->get();

        if ($cringeCounters->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.cringe.not-cringe', ['name' => "<@{$this->getOption('user_mention')}>"]));
        }

        $cringeCounters->first()->delete();
        return EmbedFactory::successEmbed($this, __('bot.cringe.reset', ['user' => $user->tag()]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
