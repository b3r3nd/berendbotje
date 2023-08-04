<?php

namespace App\Discord\Fun\Commands\Cringe;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\User;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class DecreaseCringe extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::DEL_CRINGE;
    }

    public function trigger(): string
    {
        return 'delete';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.dec-cringe');
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
        $user = User::get($this->getOption('user_mention'));
        $guildModel = \App\Domain\Discord\Guild::get($this->guildId);
        $cringeCounters = $user->cringeCounters()->where('guild_id', $guildModel->id)->get();

        if ($cringeCounters->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.cringe.not-cringe', ['name' => "<@{$this->getOption('user_mention')}>"]));
        }

        $cringeCounter = $cringeCounters->first();
        $count = $cringeCounter->count - 1;
        if ($count === 0) {
            $cringeCounter->delete();
        } else {
            $cringeCounter->count = $count;
            $cringeCounter->save();
        }

        return EmbedFactory::successEmbed($this, __('bot.cringe.change', ['name' => $user->tag(), 'count' => $count]));
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
