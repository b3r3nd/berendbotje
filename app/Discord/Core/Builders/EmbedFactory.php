<?php

namespace App\Discord\Core\Builders;

use App\Discord\Core\Bot;
use Discord\Builders\MessageBuilder;

/**
 * Includes defined presets for easy usage throughout the code.
 */
class EmbedFactory
{
    /**
     * @param string $message
     * @return MessageBuilder
     */
    public static function failedEmbed(string $message): MessageBuilder
    {
        $messageBuilder = MessageBuilder::new();
        $embed = EmbedBuilder::create(Bot::getDiscord());
        $embed->setTitle(__('bot.error'));
        $embed->setDescription($message);
        $embed->setFailed();
        $embed->setFooter(__('bot.needhelp'));
        $messageBuilder->addEmbed($embed->getEmbed());

        return $messageBuilder;
    }

    /**
     * @param string $message
     * @return MessageBuilder
     */
    public static function successEmbed(string $message): MessageBuilder
    {
        $messageBuilder = MessageBuilder::new();
        $embed = EmbedBuilder::create(Bot::getDiscord());
        $embed->setTitle(__('bot.done'));
        $embed->setFooter(__('bot.needhelp'));
        $embed->setDescription($message);
        $embed->setSuccess();
        $messageBuilder->addEmbed($embed->getEmbed());

        return $messageBuilder;
    }

}
