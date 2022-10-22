<?php

namespace App\Discord\Cringe;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\CringeCounter;
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
            $cringeCounter = CringeCounter::where(['discord_id' => $mention->id])->first();
            if ($cringeCounter) {
                $cringeCounter->count = $cringeCounter->count + 1;
                $cringeCounter->save();
            } else {
                $cringeCounter = CringeCounter::create([
                    'discord_tag' => $mention,
                    'discord_id' => $mention->id,
                    'discord_username' => $mention->username,
                    'count' => 1
                ]);
            }
            $this->message->channel->sendMessage(__('bot.cringe.change', ['name' => $cringeCounter->discord_username, 'count' => $cringeCounter->count]));
        }
    }
}
