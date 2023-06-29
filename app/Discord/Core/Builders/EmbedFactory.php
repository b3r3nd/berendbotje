<?php

namespace App\Discord\Core\Builders;

use App\Discord\Core\Bot;
use Discord\Builders\MessageBuilder;
use Discord\Discord;

/**
 * Includes defined presets for easy usage throughout the code.
 */
class EmbedFactory
{

    /**
     * When a user lacks access to a certain command we use the EPHEMERAL flag to hide the response from other users.
     *
     * @param Discord $discord
     * @param string $message
     * @return MessageBuilder
     */
    public static function lackAccessEmbed(Discord $discord, string $message): MessageBuilder
    {
        return self::failedEmbed($discord, $message)->_setFlags(00000100);

    }

    /**
     * @param Discord $discord
     * @param string $message
     * @return MessageBuilder
     */
    public static function failedEmbed(Discord $discord, string $message): MessageBuilder
    {
        $messageBuilder = MessageBuilder::new();
        $embed = EmbedBuilder::create($discord);
        $embed->setTitle(__('bot.error'));
        $embed->setDescription($message);
        $embed->setFailed();
        $embed->setFooter(__('bot.needhelp'));
        $messageBuilder->addEmbed($embed->getEmbed());

        return $messageBuilder;
    }

    /**
     * @param Discord $discord
     * @param string $message
     * @return MessageBuilder
     */
    public static function successEmbed(Discord $discord, string $message): MessageBuilder
    {
        $messageBuilder = MessageBuilder::new();
        $embed = EmbedBuilder::create($discord);
        $embed->setTitle(__('bot.done'));
        $embed->setFooter(__('bot.needhelp'));
        $embed->setDescription($message);
        $embed->setSuccess();
        $messageBuilder->addEmbed($embed->getEmbed());

        return $messageBuilder;
    }

}
