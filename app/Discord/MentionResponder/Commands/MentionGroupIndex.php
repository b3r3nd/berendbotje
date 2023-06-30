<?php

namespace App\Discord\MentionResponder\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Levels\Helpers\Helper;
use App\Discord\MentionResponder\Models\MentionGroup;
use App\Discord\Roles\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Exception;


class MentionGroupIndex extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::ADD_MENTION;
    }

    public function trigger(): string
    {
        return 'groups';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.mentiongroups');
        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->total = MentionGroup::byGuild($this->guildId)->count();
        $description = "";
        foreach (MentionGroup::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $mentionGroup) {
            $description .= Helper::getGroupName($mentionGroup);
        }

        return EmbedBuilder::create($this, __('bot.mentiongroup.title'), __('bot.mentiongroup.description', ['data' => $description]))->getEmbed();
    }
}
