<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\Admin;
use Discord\Http\Exceptions\NoPermissionsException;

class AddAdmin extends Command
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'addadmin';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiresMention = true;
        $this->requiredArguments = 2;
        $this->usageString = __('bot.admins.usage-addadmin');
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        foreach ($this->message->mentions as $mention) {
            $admin = Admin::where(['discord_id' => $mention->id])->first();
            if ($admin) {
                $this->message->channel->sendMessage(__('bot.admins.exists'));
                return;
            }

            if (!Admin::hasHigherLevel($this->message->author->id, $this->arguments[1])) {
                $this->message->channel->sendMessage(__('bot.admins.lack-access'));
                return;
            }
            Admin::create([
                'discord_id' => $mention->id,
                'discord_username' => $mention->username,
                'level' => $this->arguments[1]
            ]);
            $this->message->channel->sendMessage(__('bot.admins.added'));
        }
    }
}
