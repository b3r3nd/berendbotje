<?php

namespace App\Discord\Fun\Commands\Cringe;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\User;
use App\Domain\Fun\Models\CringeCounter;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class IncreaseCringe extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::ADD_CRINGE;
    }

    public function trigger(): string
    {
        return 'add';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.inc-cringe');
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
        $fail = false;


        if (in_array($user->discord_id, ['259461260645629953', '651378995245613056', '1034642309289091207'], true)) {
            $user = User::get($this->interaction->member);
            $fail = true;
        }

        $cringeCounters = $user->cringeCounters()->where('guild_id', $guildModel->id)->get();
        if ($cringeCounters->isEmpty()) {
            $cringeCounter = new CringeCounter(['count' => 1, 'guild_id' => $guildModel->id]);
            $user->cringeCounters()->save($cringeCounter);
        } else {
            $cringeCounter = $cringeCounters->first();
            $cringeCounter->update(['count' => $cringeCounter->count + 1]);
        }

        $cringeCounter->refresh();
        if ($fail) {
            return EmbedFactory::successEmbed($this, __('bot.cringe.fail', ['name' => $user->tag(), 'count' => $cringeCounter->count]));
        }
        return EmbedFactory::successEmbed($this, __('bot.cringe.change', ['name' => $user->tag(), 'count' => $cringeCounter->count]));
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
