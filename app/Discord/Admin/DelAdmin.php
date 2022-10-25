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

class DelAdmin extends SlashCommand
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
        $this->requiresMention = true;
        $this->requiredArguments = 1;
        $this->usageString = __('bot.admins.usage-deladmin');
        $this->description = __('bot.admins.desc-deladmin');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => true,
            ]
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $admin = AdminHelper::validateAdmin($this->arguments[0], $this->commandUser);
        if ($admin instanceof Admin) {
            $admin->delete();
            return EmbedFactory::successEmbed(__('bot.admins.deleted'));
        } else {
            return EmbedFactory::failedEmbed($admin);
        }
    }
}
