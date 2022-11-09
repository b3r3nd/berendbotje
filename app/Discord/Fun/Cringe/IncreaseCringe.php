<?php

namespace App\Discord\Fun\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Guild;
use App\Models\CringeCounter;
use App\Models\DiscordUser;
use App\Models\KickCounter;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class IncreaseCringe extends SlashAndMessageCommand
{
    public function permission(): string
    {
        return 'add-cringe';
    }

    public function trigger(): string
    {
        return 'addcringe';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->requiresMention = true;
        $this->usageString = __('bot.cringe.usage-addcringe');
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
        $user = DiscordUser::get($this->arguments[0]);
        $guildModel = \App\Models\Guild::get($this->guildId);

        $cringeCounters = $user->cringeCounters()->where('guild_id', $guildModel->id)->get();

        if ($cringeCounters->isEmpty()) {
            $cringeCounter = new CringeCounter(['count' => 1, 'guild_id' => $guildModel->id]);
            $user->cringeCounters()->save($cringeCounter);
        } else {
            $cringeCounter = $cringeCounters->first();
            $cringeCounter->update(['count' => $cringeCounter->count + 1]);
        }

        $cringeCounter->refresh();
        return EmbedFactory::successEmbed(__('bot.cringe.change', ['name' => $user->tag(), 'count' => $cringeCounter->count]));
    }
}
