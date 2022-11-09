<?php

namespace App\Discord\Fun\Command;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\Permission;
use App\Models\Guild;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class DeleteCommand extends SlashAndMessageCommand
{
    public function permission(): Permission
    {
        return Permission::COMMANDS;
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
        \App\Models\Command::where(['trigger' => $this->arguments[0], 'guild_id' => Guild::get($this->guildId)->id])->delete();
        Bot::get()->getGuild($this->guildId)->deleteCommand($this->arguments[0]);
        return EmbedFactory::successEmbed(__('bot.cmd.deleted', ['trigger' => $this->arguments[0]]));
    }
}
