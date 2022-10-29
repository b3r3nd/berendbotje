<?php

namespace App\Discord\Core\Command;

use App\Discord\Core\AccessLevels;
use Discord\Parts\Channel\Message;

/**
 * Abstract class for all properties and methods shared across all commands, regardless whether it's a slash or message
 * command, or both in one.
 *
 * @property AccessLevels $accessLevels     Required access level for this command.
 * @property string $trigger                Trigger for the command, both slash and text.
 * @property array $arguments               Array of all the given arguments by either slash or text commands.
 * @property string $message                If available set with the Message instance received.
 * @property string $commandUser            Discord ID of user using the command.
 *
 */
abstract class Command
{
    protected AccessLevels $accessLevel;
    protected string $trigger;
    protected array $arguments = [];
    protected Message $message;
    protected string $commandUser;

    public abstract function accessLevel(): AccessLevels;

    public abstract function trigger(): string;

    public function __construct()
    {
        $this->accessLevel = $this->accessLevel();
        $this->trigger = $this->trigger();
    }

}
