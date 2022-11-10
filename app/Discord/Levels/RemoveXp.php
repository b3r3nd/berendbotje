<?php

namespace App\Discord\Levels;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;

class RemoveXp extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_XP;
    }

    public function trigger(): string
    {
        return 'removexp';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->requiresMention = 1;
        $this->usageString = __('bot.xp.usage-delxp');
        parent::__construct();
    }

    public function action(): void
    {
        (new UpdateMessageCounterAction($this->message, $this->arguments[0], $this->arguments[1], true))->execute();
        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.xp.removed', ['user' => $this->arguments[0], 'xp' => $this->arguments[1]])));
    }
}
