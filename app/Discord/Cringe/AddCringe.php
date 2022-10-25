<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Models\Bumper;
use App\Models\CringeCounter;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class AddCringe extends SlashCommand
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
        $user = DiscordUser::firstOrCreate([
            'discord_id' => $this->arguments[0],
            'discord_tag' => "<@{$this->arguments[0]}>",
        ]);
        if ($user->cringeCounter) {
            $user->cringeCounter()->update(['count' => $user->cringeCounter->count + 1]);
        } else {
            $user->cringeCounter()->save(new CringeCounter(['count' => 1]));
        }
        $user->refresh();
        return EmbedFactory::successEmbed(__('bot.cringe.change', ['name' => $user->discord_tag, 'count' => $user->cringeCounter->count]));

    }
}
