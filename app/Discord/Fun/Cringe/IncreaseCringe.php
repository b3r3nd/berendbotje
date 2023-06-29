<?php

namespace App\Discord\Fun\Cringe;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\CringeCounter;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class IncreaseCringe extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::ADD_CRINGE;
    }

    public function trigger(): string
    {
        return 'addcringe';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.inc-cringe');
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

    /**
     * @throws NoPermissionsException
     */
    public function action(): MessageBuilder
    {
        $user = DiscordUser::get($this->getOption('user_mention'));
        $guildModel = \App\Models\Guild::get($this->guildId);
        $fail = false;


        if (in_array($user->discord_id, ['259461260645629953', '651378995245613056', '1034642309289091207'], true)) {
            $user = DiscordUser::get($this->commandUser);
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
            return EmbedFactory::successEmbed($this->discord, __('bot.cringe.fail', ['name' => $user->tag(), 'count' => $cringeCounter->count]));
        }
        return EmbedFactory::successEmbed($this->discord, __('bot.cringe.change', ['name' => $user->tag(), 'count' => $cringeCounter->count]));
    }
}
