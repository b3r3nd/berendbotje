<?php

namespace App\Discord\Roles;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\MessageCommand;
use App\Models\DiscordUser;
use Discord\Builders\MessageBuilder;

class UserRoles extends MessageCommand
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
        $this->requiredArguments = 1;
        $this->requiresMention = 1;
        $this->usageString = __('bot.roles.usage-userroles');

        parent::__construct();
    }

    public function action(): void
    {
        $description = "";
        if (DiscordUser::get($this->arguments[0])->roles->isEmpty()) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.myroles.none')));
            return;
        }
        foreach (DiscordUser::get($this->arguments[0])->rolesByGuild($this->guildId) as $role) {
            $description .= "{$role->name}\n";
        }

        $this->message->channel->sendMessage(MessageBuilder::new()->addEmbed(EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.myroles.title'))
            ->setFooter(__('bot.myroles.footer'))
            ->setDescription(__('bot.myroles.description', ['roles' => $description]))
            ->getEmbed()));
    }
}
