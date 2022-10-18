<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Models\Admin;
use Discord\Http\Exceptions\NoPermissionsException;

class DelAdmin extends Command
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'deladmin';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiresMention = true;
        $this->requiredArguments = 1;
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        foreach ($this->message->mentions as $mention) {
            $admin = AdminHelper::validateAdmin($mention->id, $this->message->author->id);
            if ($admin instanceof Admin) {
                $admin->delete();
                $this->message->channel->sendMessage(__('bot.admins.deleted'));
            } else {
                $this->message->channel->sendMessage($admin);
            }
        }
    }
}
