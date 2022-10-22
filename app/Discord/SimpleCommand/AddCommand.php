<?php

namespace App\Discord\SimpleCommand;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use Discord\Http\Exceptions\NoPermissionsException;
use Illuminate\Support\Facades\DB;

class AddCommand extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'addcmd';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 2;
        $this->usageString = __('bot.cmd.usage-addcmd');
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        $command = \App\Models\Command::create(['trigger' => $this->arguments[0], 'response' => $this->arguments[1]]);
        $command->save();
        new SimpleCommand(Bot::get(), $this->arguments[0], $this->arguments[1]);
        $this->message->channel->sendMessage(__('bot.cmd.saved'));
    }
}
