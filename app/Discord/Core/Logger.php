<?php

namespace App\Discord\Core;

use App\Discord\Core\Builders\EmbedBuilder;
use Carbon\Carbon;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;
use Exception;

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
     * @param $embedBuilder
     * @param $type
     * @return void
     */
    private function sendEmbed($embedBuilder, $type): void
    {
        if ($type == 'fail') {
            $embedBuilder->setFailed();
        } elseif ($type == 'success') {
            $embedBuilder->setSuccess();
        } elseif ($type == 'warning') {
            $embedBuilder->setWarning();
        } else {
            $embedBuilder->setLog();
        }

        $channel = Bot::getDiscord()->getChannel($this->logChannelId);
        $channel?->sendEmbed($embedBuilder->getEmbed());
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
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord());

        if ($member instanceof Member) {
            $embedBuilder->getEmbed()
                ->setThumbnail($member->user->avatar)
                ->setAuthor($member->user->displayname, $member->user->avatar);
        } else {
            $embedBuilder->getEmbed()
                ->setThumbnail($member->avatar)
                ->setAuthor($member->displayname, $member->avatar);
        }

        $embedBuilder->getEmbed()
            ->setDescription($description)
            ->setTimestamp();

        $this->sendEmbed($embedBuilder, $type);
    }

    /**
     * @param string $message
     * @param string $type
     * @return void
     */
    public function log(string $message, string $type): void
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setDescription($message)
            ->setFooter(Carbon::now()->toTimeString());

        $this->sendEmbed($embedBuilder, $type);
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
