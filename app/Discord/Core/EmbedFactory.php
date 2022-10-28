<?php

namespace App\Discord\Core;

use Carbon\Carbon;
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
        $embed->setFooter(Carbon::now()->format('H:i:s'));
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
        $embed->setFooter(Carbon::now()->format('H:i:s'));
        $embed->setDescription($message);
        $embed->setSuccess();
        $messageBuilder->addEmbed($embed->getEmbed());

        return $messageBuilder;
    }

}
