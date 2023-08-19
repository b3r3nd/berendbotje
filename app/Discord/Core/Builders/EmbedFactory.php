<?php

namespace App\Discord\Core\Builders;

use App\Discord\Core\SlashCommand;
use Discord\Builders\MessageBuilder;
use Exception;

class EmbedFactory
{
    /**
     * When a user lacks access to a certain command we use the EPHEMERAL flag to hide the response from other users.
     *
     * @param SlashCommand $command
     * @param string $message
     * @return MessageBuilder
     * @throws Exception
     */
    public static function lackAccessEmbed(SlashCommand $command, string $message): MessageBuilder
    {
        return self::failedEmbed($command, $message)->_setFlags(00000100);

    }

    /**
     * @param SlashCommand $command
     * @param string $message
     * @return MessageBuilder
     * @throws Exception
     */
    public static function failedEmbed(SlashCommand $command, string $message): MessageBuilder
    {
        $messageBuilder = MessageBuilder::new();
        $embed = EmbedBuilder::create($command);
        $embed->setTitle(__('bot.error'));
        $embed->setDescription($message);
        $embed->setFailed();
        $messageBuilder->addEmbed($embed->getEmbed());

        return $messageBuilder;
    }

    /**
     * @param SlashCommand $command
     * @param string $message
     * @return MessageBuilder
     * @throws Exception
     */
    public static function successEmbed(SlashCommand $command, string $message): MessageBuilder
    {
        $messageBuilder = MessageBuilder::new();
        $embed = EmbedBuilder::create($command);
        $embed->setTitle(__('bot.done'));
        $embed->setDescription($message);
        $embed->setSuccess();
        $messageBuilder->addEmbed($embed->getEmbed());

        return $messageBuilder;
    }

}
