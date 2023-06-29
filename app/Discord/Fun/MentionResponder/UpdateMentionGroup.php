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

class UpdateMentionGroup extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_MENTION_GROUP;
    }

    public function trigger(): string
    {
        return 'updategroup';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.updategroup');

        $this->slashCommandOptions = [
            [
                'name' => 'id',
                'description' => 'Group ID',
                'type' => Option::INTEGER,
                'required' => true,
            ],
            [
                'name' => 'group_type',
                'description' => 'User Or Group',
                'type' => Option::STRING,
                'required' => true,
                'choices' => [
                    ['name' => "Role", 'value' => 'has_role'],
                    ['name' => "User", 'value' => 'has_user']
                ],
            ],
            [
                'name' => 'multiplier',
                'description' => 'Usage Mutliplier',
                'type' => Option::INTEGER,
                'required' => false,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $group = MentionGroup::find($this->arguments[0]);
        if (!$group) {
            return EmbedFactory::failedEmbed(__('bot.mentiongroup.notexist', ['group' => $this->arguments[0]]));
        }

        (new UpdateMentionGroupAction($group, $this->arguments))->execute();

        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed(__('bot.mentiongroup.updated', ['group' => $this->arguments[0]]));
    }
}
