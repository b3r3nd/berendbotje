<?php

namespace App\Discord\Moderation\Command;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Guild;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;

class DeleteCommand extends SlashCommand
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
        $this->description = __('bot.slash.del-command');
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
     * @return MessageBuilder
     */
    public function action(): MessageBuilder
    {
        \App\Models\Command::where(['trigger' => $this->getOption('command'), 'guild_id' => Guild::get($this->guildId)->id])->delete();
        return EmbedFactory::successEmbed($this->discord, __('bot.cmd.deleted', ['trigger' => $this->getOption('command')]));
    }
}
