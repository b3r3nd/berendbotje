<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\Admin;
use Discord\Http\Exceptions\NoPermissionsException;

class UpdateAdmin extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'clvladmin';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiresMention = true;
        $this->requiredArguments = 2;
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        foreach ($this->message->mentions as $mention) {
            $admin = Admin::where(['discord_id' => $mention->id])->first();
            if (!$admin) {
                $this->message->channel->sendMessage(__('bot.admins.not-exist'));
                return;
            }
            if (!Admin::hasHigherLevel($this->message->author->id, $admin->level)) {
                $this->message->channel->sendMessage(__('bot.admins.powerful', ['name' => $admin->discord_username]));
                return;
            }
            $admin->update(['level' => $this->arguments[1]]);
            $this->message->channel->sendMessage(__('bot.admins.changed'));
        }
    }
}
