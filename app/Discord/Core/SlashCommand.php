<?php

namespace App\Discord\Core;

use App\Discord\Core\Traits\SlashCommandTrait;
use Discord\Builders\MessageBuilder;

/**
 * Extendable class to easily create new Slash ONLY commands. For better understanding:
 * @see Command
 * @see SlashCommandTrait
 */
abstract class SlashCommand extends Command
{
    use SlashCommandTrait;

    public abstract function action(): MessageBuilder;

}
