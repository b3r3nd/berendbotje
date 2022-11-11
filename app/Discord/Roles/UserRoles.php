<?php

namespace App\Discord\Roles;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\DiscordUser;
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
        return 'userroles';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.userroles');
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
        $description = "";
        if (DiscordUser::get($this->arguments[0])->roles->isEmpty()) {
            return EmbedFactory::failedEmbed(__('bot.myroles.none'));
        }
        foreach (DiscordUser::get($this->arguments[0])->rolesByGuild($this->guildId) as $role) {
            $description .= "{$role->name}\n";
        }

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.userroles.title'))
            ->setFooter(__('bot.userroles.footer'))
            ->setDescription(__('bot.userroles.description', ['roles' => $description]))
            ->getEmbed());
    }
}
