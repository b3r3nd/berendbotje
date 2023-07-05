<?php

namespace App\Discord\Moderation\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Moderation\Models\Abuser;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class Block extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::BLACKLIST;
    }

    public function trigger(): string
    {
        return 'block';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.block');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => __('bot.user-mention'),
                'type' => Option::USER,
                'required' => true,
            ],
            [
                'name' => 'reason',
                'description' => __('bot.reason'),
                'type' => Option::STRING,
                'required' => true,
            ],
        ];

        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $userId = $this->getOption('user_mention');

        if(!Abuser::where('discord_id', $userId)->get()->isEmpty()) {
            return EmbedFactory::failedEmbed($this, __('bot.blacklist.blocked',['user' => "<@{$userId}>"]));
        }

        $abuser = Abuser::create(['discord_id' => $userId, 'guild_id' => $this->guildId, 'reason' => $this->getOption('reason')]);
        return EmbedFactory::successEmbed($this, __('bot.blacklist.block',['user' => "<@{$userId}>"]));
    }
}
