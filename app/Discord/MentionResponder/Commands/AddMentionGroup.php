<?php

namespace App\Discord\MentionResponder\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\Guild;
use App\Discord\Core\SlashCommand;
use App\Discord\MentionResponder\Actions\UpdateMentionGroupAction;
use App\Discord\MentionResponder\Models\MentionGroup;
use App\Discord\Roles\Enums\Permission;
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
            [
                'name' => 'multiplier',
                'description' => 'Usage Mutliplier',
                'type' => Option::INTEGER,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $group = MentionGroup::create(['name' => $this->getOption('id'), 'guild_id' => Guild::get($this->guildId)->id]);
        (new UpdateMentionGroupAction($group, $this->interaction->data->options))->execute();
        $this->bot->getGuild($this->guildId)?->mentionResponder->loadReplies();
        return EmbedFactory::successEmbed($this->discord, __('bot.mentiongroup.added', ['group' => $this->getOption('id')]));
    }
}
