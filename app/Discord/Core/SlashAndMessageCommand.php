<?php

namespace App\Discord\Core;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Traits\MessageCommandTrait;
use App\Discord\Core\Traits\SlashCommandTrait;
use Discord\Builders\MessageBuilder;

/**
 * Extendable class to easily create new slash and message commands in one. For better understanding:
 * @see Command
 * @see SlashCommandTrait
 * @see MessageCommandTrait
 *
 * The class will only send Embeds back to discord, nothing else. There is an EmbedBuilder and EmbedFactory to more
 * quickly and easily return Embeds
 * @see EmbedBuilder
 * @see EmbedFactory
 */
abstract class SlashAndMessageCommand extends Command
{
    use MessageCommandTrait, SlashCommandTrait;

    public abstract function action(): MessageBuilder;
}
