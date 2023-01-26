<?php

namespace App\Discord\Fun\MentionResponder;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\Guild;
use App\Models\MentionGroup;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class AddMentionGroup extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_MENTION_GROUP;
    }

    public function trigger(): string
    {
        return 'addgroup';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.addgroup');

        $this->slashCommandOptions = [
            [
                'name' => 'group_id',
                'description' => 'Group ID',
                'type' => Option::ROLE,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        MentionGroup::create(['name' => $this->arguments[0], 'guild_id' => Guild::get($this->guildId)->id]);
        Bot::get()->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed(__('bot.mentiongroup.added', ['group' => $this->arguments[0]]));
    }
}
