<?php

namespace App\Discord\Fun\MentionResponder;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Helper;
use App\Models\MentionGroup;

use Discord\Parts\Embed\Embed;


class MentionGroupIndex extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::MANAGE_MENTION_GROUP;
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
     */
    public function getEmbed(): Embed
    {
        $this->total = MentionGroup::byGuild($this->guildId)->count();
        $description = "";
        foreach (MentionGroup::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $mentionGroup) {
            $description .= Helper::getGroupName($mentionGroup);
        }

        return EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.mentiongroup.title'))
            ->setFooter(__('bot.mentiongroup.footer'))
            ->setDescription(__('bot.mentiongroup.description', ['data' => $description]))
            ->getEmbed();
    }
}
