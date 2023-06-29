<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class UserRoles extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'role';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.userroles');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => 'Mention',
                'type' => Option::USER,
                'required' => false,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $description = "";

        $user = DiscordUser::get($this->getOption('user_mention') ?? $this->commandUser);

        if ($user->roles->isEmpty()) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.userroles.none', ['user' => $user->tag()]));
        }
        foreach ($user->rolesByGuild($this->guildId) as $role) {
            $description .= "{$role->name}\n";
        }

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create($this->bot->discord)
            ->setTitle(__('bot.userroles.title'))
            ->setFooter(__('bot.userroles.footer'))
            ->setDescription(__('bot.userroles.description', ['roles' => $description, 'user' => $user->tag()]))
            ->getEmbed());
    }
}
