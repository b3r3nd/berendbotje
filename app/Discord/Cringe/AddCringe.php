<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\Bumper;
use App\Models\CringeCounter;
use App\Models\DiscordUser;
use Discord\Http\Exceptions\NoPermissionsException;

class AddCringe extends Command
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
        parent::__construct();
        $this->requiredArguments = 1;
        $this->requiresMention = true;
        $this->usageString = __('bot.cringe.usage-addcringe');

    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        foreach ($this->message->mentions as $mention) {
            $user = DiscordUser::firstOrCreate([
                'discord_id' => $mention->id,
                'discord_tag' => $mention,
            ]);
            if ($user->cringeCounter) {
                $user->cringeCounter()->update(['count' => $user->cringeCounter->count + 1]);
            } else {
                $user->cringeCounter()->save(new CringeCounter(['count' => 1]));
            }

            $user->refresh();

            $this->message->channel->sendMessage(__('bot.cringe.change', ['name' => $user->discord_username, 'count' => $user->cringeCounter->count]));
        }
    }
}
