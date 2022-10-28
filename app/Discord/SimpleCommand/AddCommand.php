<?php

namespace App\Discord\SimpleCommand;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Bot;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\SlashCommand;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Interactions\Command\Option;
use Illuminate\Support\Facades\DB;

class AddCommand extends SlashCommand
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
        $this->requiredArguments = 2;
        $this->usageString = __('bot.cmd.usage-addcmd');
        $this->slashCommandOptions = [
            [
                'name' => 'command',
                'description' => 'Command',
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'response',
                'description' => 'Response',
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
        $trigger = array_shift($this->arguments);
        $response = join(' ', $this->arguments);
        $command = \App\Models\Command::create(['trigger' => $trigger, 'response' => $response]);
        $command->save();
        new SimpleCommand(Bot::get(), $trigger, $response);
        return EmbedFactory::successEmbed(__('bot.cmd.saved', ['trigger' => $trigger, 'response' => $response]));
    }
}
