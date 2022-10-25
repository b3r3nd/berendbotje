<?php

namespace App\Discord\Core;

use Discord\Builders\MessageBuilder;

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
        $embed->setDescription($message);
        $embed->setFailed();
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
        $embed->setDescription($message);
        $embed->setSuccess();
        $messageBuilder->addEmbed($embed->getEmbed());

        return $messageBuilder;
    }

}
