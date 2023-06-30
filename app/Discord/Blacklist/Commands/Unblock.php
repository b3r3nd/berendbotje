<?php

namespace App\Discord\Blacklist\Commands;

use App\Discord\Blacklist\Models\Abuser;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class Unblock extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ABUSERS;
    }

    public function trigger(): string
    {
        return 'unblock';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.unblock');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => true,
            ],
        ];

        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $userId = $this->getOption('user_mention');

        if(Abuser::where('discord_id', $userId)->get()->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.blacklist.unblocked',['user' => "<@{$userId}>"]));
        }

        $abusers = Abuser::where('discord_id', $userId)->first();
        $abusers->delete();
        return EmbedFactory::successEmbed($this, __('bot.blacklist.unblock',['user' => "<@{$userId}>"]));
    }
}
