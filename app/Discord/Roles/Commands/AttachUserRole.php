<?php

namespace App\Discord\Roles\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\User;
use App\Domain\Permission\Enums\Permission;
use App\Domain\Permission\Models\Role;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class AttachUserRole extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::ATTACH_ROLE;
    }

    public function trigger(): string
    {
        return 'add';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.attach-user-role');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => __('bot.user-mention'),
                'type' => Option::USER,
                'required' => true,
            ],
            [
                'name' => 'role_name',
                'description' => __('bot.role'),
                'type' => Option::STRING,
                'required' => true,
                'autocomplete' => true,
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
        if (!Role::exists($this->guildId, $this->getOption('role_name'))) {
            return EmbedFactory::failedEmbed($this, __('bot.roles.not-exist', ['role' => $this->getOption('role_name')]));
        }
        $role = Role::get($this->guildId, $this->getOption('role_name'));

        $user = User::get($this->getOption('user_mention'));
        $user->roles()->attach($role);

        return EmbedFactory::successEmbed($this, __('bot.roles.role-attached', ['role' => $role->name, 'user' => $user->tag()]));
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return $this->getAutoComplete(Role::class, $interaction->guild_id, 'name', $this->getOption('role_name'));
    }
}
