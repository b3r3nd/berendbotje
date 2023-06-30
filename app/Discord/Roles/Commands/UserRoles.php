<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

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

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $description = "";

        $user = DiscordUser::get($this->getOption('user_mention') ?? $this->interaction->member->id);

        if ($user->roles->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.userroles.none', ['user' => $user->tag()]));
        }
        foreach ($user->rolesByGuild($this->guildId) as $role) {
            $description .= "{$role->name}\n";
        }

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create($this, __('bot.userroles.title'), __('bot.userroles.description', ['roles' => $description, 'user' => $user->tag()]))->getEmbed());
    }
}
