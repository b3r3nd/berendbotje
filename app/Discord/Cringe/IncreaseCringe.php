<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\CringeCounter;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class IncreaseCringe extends SlashAndMessageCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::USER;
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
        $user = DiscordUser::firstOrCreate(['discord_id' => $this->arguments[0]]);
        if ($user->cringeCounter) {
            $user->cringeCounter()->update(['count' => $user->cringeCounter->count + 1]);
        } else {
            $user->cringeCounter()->save(new CringeCounter(['count' => 1]));
        }
        $user->refresh();
        return EmbedFactory::successEmbed(__('bot.cringe.change', ['name' => $user->tag(), 'count' => $user->cringeCounter->count]));
    }
}
