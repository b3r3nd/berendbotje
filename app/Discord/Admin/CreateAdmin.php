<?php

namespace App\Discord\Admin;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\SlashAndMessageCommand;
use App\Discord\Core\Command\SlashCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\Admin;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class CreateAdmin extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'addadmin';
    }

    public function __construct()
    {
        $this->requiredArguments = 2;
        $this->requiresMention = true;
        $this->usageString = __('bot.admins.usage-addadmin');
        $this->description = __('bot.admins.desc-addadmin');
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
        if (DiscordUser::isAdmin($this->arguments[0])) {
            return EmbedFactory::failedEmbed(__('bot.admins.exists'));
        }
        if (!DiscordUser::hasHigherLevel($this->commandUser, $this->arguments[1])) {
            return EmbedFactory::failedEmbed(__('bot.admins.lack-access'));
        }
        $user = DiscordUser::firstOrCreate([
            'discord_id' => $this->arguments[0],
            'discord_tag' => "<@{$this->arguments[0]}>",
        ]);
        $user->admin()->save(new Admin(['user_id' => $user->id, 'level' => $this->arguments[1]]));
        return EmbedFactory::successEmbed(__('bot.admins.added', ['user' => $user->discord_tag, 'level' => $this->arguments[1]]));
    }
}
