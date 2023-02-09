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
                'name' => 'id',
                'description' => 'Group or User ID',
                'type' => Option::STRING,
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
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $group = MentionGroup::create(['name' => $this->arguments[0], 'guild_id' => Guild::get($this->guildId)->id]);
        $group->update([$this->arguments[1] => true]);
        Bot::get()->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed(__('bot.mentiongroup.added', ['group' => $this->arguments[0]]));
    }
}
