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
        $this->usageString = __('bot.admins.usage-clvladmin');
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        foreach ($this->message->mentions as $mention) {
            $admin = AdminHelper::validateAdmin($mention->id, $this->message->author->id);
            if ($admin instanceof Admin) {
                $admin->update(['level' => $this->arguments[1]]);
                $this->message->channel->sendMessage(__('bot.admins.changed'));
            } else {
                $this->message->channel->sendMessage($admin);
            }
        }
    }
}
