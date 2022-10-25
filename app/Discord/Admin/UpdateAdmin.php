<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command;
use App\Discord\Core\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Models\Admin;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

class UpdateAdmin extends SlashCommand
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
        $this->requiresMention = true;
        $this->requiredArguments = 2;
        $this->usageString = __('bot.admins.usage-clvladmin');
        $this->description = __('bot.admins.desc-clvladmin');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => true,
            ],
            [
                'name' => 'access_level',
                'description' => 'Access',
                'type' => Option::INTEGER,
                'required' => true,
            ]
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $admin = AdminHelper::validateAdmin($this->arguments[0], $this->commandUser);
        if ($admin instanceof Admin) {
            $admin->update(['level' => $this->arguments[1]]);
            return EmbedFactory::successEmbed(__('bot.admins.changed', ['user' => "<@{$this->arguments[0]}>", 'level' => $this->arguments[1]]));
        } else {
            return EmbedFactory::failedEmbed($admin);
        }
    }
}
