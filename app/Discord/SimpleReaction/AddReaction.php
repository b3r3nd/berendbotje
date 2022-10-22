<?php

namespace App\Discord\SimpleReaction;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Models\Reaction;
use Discord\Http\Exceptions\NoPermissionsException;

class AddReaction extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'addreaction';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 2;
        $this->usageString = __('bot.reactions.usage-addreaction');
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        $this->message->channel->sendMessage(__('bot.reactions.saved'));
        $command = Reaction::create(['trigger' => $this->arguments[0], 'reaction' => $this->arguments[1]]);
        $command->save();
        new SimpleReaction(Bot::get(), $this->arguments[0], $this->arguments[1]);
    }
}
