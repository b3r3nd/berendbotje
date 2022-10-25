<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\CringeCounter;
use App\Models\DiscordUser;
use Discord\Http\Exceptions\NoPermissionsException;

class DelCringe extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'delcringe';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 1;
        $this->requiresMention = true;
        $this->usageString = __('bot.cringe.usage-delcringe');
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        foreach ($this->message->mentions as $mention) {
            $user = DiscordUser::where(['discord_id' => $mention->id])->first();

            if (!$user->has('cringeCounter')->get()->isEmpty()) {
                $user->cringeCounter->count = $user->cringeCounter->count - 1;
                if ($user->cringeCounter->count == 0) {
                    $user->cringeCounter->delete();
                } else {
                    $user->cringeCounter->save();
                }
            } else {
                $this->message->channel->sendMessage(__('bot.cringe.not-cringe', ['name' => $this->arguments[0]]));
            }
            $this->message->channel->sendMessage((__('bot.cringe.change', ['name' => $user->discord_tga, 'count' => $user->cringeCounter->count])));
        }
    }
}
