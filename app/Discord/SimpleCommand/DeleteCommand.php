<?php

namespace App\Discord\SimpleCommand;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class DeleteCommand extends SlashAndMessageCommand
{
    public function accessLevel(): AccessLevels
    {
        return AccessLevels::MOD;
    }

    public function trigger(): string
    {
        return 'delcmd';
    }

    public function __construct()
    {
        $this->requiredArguments = 1;
        $this->usageString = __('bot.cmd.usage-delcmd');
        $this->slashCommandOptions = [
            [
                'name' => 'command',
                'description' => 'Command',
                'type' => Option::STRING,
                'required' => true,
            ]
        ];
        parent::__construct();
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): MessageBuilder
    {
        \App\Models\Command::where(['trigger' => $this->arguments[0]])->delete();
        Bot::get()->deleteCommand($this->arguments[0]);
        return EmbedFactory::successEmbed(__('bot.cmd.deleted', ['trigger' => $this->arguments[0]]));
    }
}
