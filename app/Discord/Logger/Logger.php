<?php

namespace App\Discord\Logger;

use App\Discord\Core\Builders\EmbedBuilder;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Embed\Embed;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;
use Exception;

class Logger
{
    private string $logChannelId;
    private Discord $discord;

    /**
     * @param string $logChannelId
     * @param Discord $discord
     */
    public function __construct(string $logChannelId, Discord $discord)
    {
        $this->logChannelId = $logChannelId;
        $this->discord = $discord;
    }

    /**
     * @param Embed $embed
     * @param $type
     * @return void
     */
    private function sendEmbed(Embed $embed, $type): void
    {
        if ($type === 'fail') {
            $embed->setColor(15548997);
        } elseif ($type === 'success') {
            $embed->setColor(2067276);
        } elseif ($type === 'warning') {
            $embed->setColor(15105570);
        } else {
            $embed->setColor(3447003);
        }
        $channel = $this->discord->getChannel($this->logChannelId);
        $channel?->sendEmbed($embed);
    }

    /**
     * @param Member|User $member
     * @param string $description
     * @param string $type
     * @return void
     * @throws Exception
     */
    public function logWithMember(Member|User $member, string $description, string $type): void
    {
        $embed = EmbedBuilder::createForLog($this->discord);
        if ($member instanceof Member) {
            $embed->setThumbnail($member->user->avatar)->setAuthor($member->user->displayname, $member->user->avatar);
        } else {
            $embed->setThumbnail($member->avatar)->setAuthor($member->displayname, $member->avatar);
        }
        $embed->setDescription($description)->setTimestamp();
        $this->sendEmbed($embed, $type);
    }

    /**
     * @param string $message
     * @param string $type
     * @return void
     * @throws Exception
     */
    public function log(string $message, string $type): void
    {
        $embed = EmbedBuilder::createForLog($this->discord)->setDescription($message);

        $this->sendEmbed($embed, $type);
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
