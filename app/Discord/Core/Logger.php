<?php

namespace App\Discord\Core;

use App\Discord\Core\Builders\EmbedBuilder;
use Carbon\Carbon;

class Logger
{
    private string $logChannelId;

    /**
     * @param string $logChannelId
     */
    public function __construct(string $logChannelId)
    {
        $this->logChannelId = $logChannelId;
    }

    /**
     * @param string $message
     * @param string $title
     * @param string $type
     * @return void
     */
    public function log(string $message, string $title, string $type): void
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle($title)
            ->setDescription($message)
            ->setFooter(Carbon::now()->toTimeString());


        if ($type == 'fail') {
            $embedBuilder->setFailed();
        } elseif ($type == 'success') {
            $embedBuilder->setSuccess();
        } elseif ($type == 'warning') {
            $embedBuilder->setWarning();
        } else {
            $embedBuilder->setLog();
        }

        Bot::getDiscord()->getChannel($this->logChannelId)->sendEmbed($embedBuilder->getEmbed());
    }

    /**
     * @param string $logChannelId
     * @return void
     */
    public function setLogChannelId(string $logChannelId): void
    {
        $this->logChannelId = $logChannelId;
    }
}
