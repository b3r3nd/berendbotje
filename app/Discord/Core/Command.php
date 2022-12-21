<?php

namespace App\Discord\Core;

use App\Discord\Core\Enums\Permission;

/**
 * Abstract class for all properties and methods shared across all commands, regardless whether it's a slash or message
 * command, or both in one.
 *
 * @property string $permission             Required permission level for this command.
 * @property string $trigger                Trigger for the command, both slash and text.
 * @property array $arguments               Array of all the given arguments by either slash or text commands.
 * @property string $guildId                String of the Discord Guild ID
 * @property string $commandUser            ID of the user using the command
 */
abstract class Command
{
    protected Permission $permission;
    protected string $trigger;
    protected array $arguments = [];
    protected string $guildId = '';
    protected string $commandUser;

    abstract public function permission(): Permission;

    abstract public function trigger(): string;

    public function __construct()
    {
        $this->permission = $this->permission();
        $this->trigger = $this->trigger();
    }

    /**
     * @return string
     */
    public function getCommandUser(): string
    {
        return $this->commandUser;
    }

}
