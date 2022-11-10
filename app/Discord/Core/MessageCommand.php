<?php

namespace App\Discord\Core;

use App\Discord\Core\Traits\MessageCommandTrait;

/**
 * Extendable class to easily create new message only commands. For better understanding:
 * @see Command
 * @see MessageCommandTrait
 */
abstract class MessageCommand extends Command
{
    use MessageCommandTrait;

    public abstract function action(): void;
}
