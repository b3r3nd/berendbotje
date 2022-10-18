<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\CringeCounter;
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
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        foreach ($this->message->mentions as $mention) {
            $cringeCounter = CringeCounter::where(['discord_id' => $mention->id])->first();
            if ($cringeCounter) {
                $cringeCounter->count = $cringeCounter->count - 1;
                if ($cringeCounter->count == 0) {
                    $cringeCounter->delete();
                } else {
                    $cringeCounter->save();
                }
            } else {
                $this->message->channel->sendMessage(__('bot.cringe.not-cringe', ['name' => $this->arguments[0]]));
            }
            $this->message->channel->sendMessage((__('bot.cringe.change', ['name' => $cringeCounter->discord_username, 'count' => $cringeCounter->count])));
        }
    }
}
