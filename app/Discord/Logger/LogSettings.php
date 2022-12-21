<?php

namespace App\Discord\Logger;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use App\Models\LogSetting;
use Discord\Builders\MessageBuilder;

class LogSettings extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::LOGS;
    }

    public function trigger(): string
    {
        return 'logconfig';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.logconfig');
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setTitle(__('bot.logset.title'))
            ->setFooter(__('bot.logset.footer'));

        $description = "";
        foreach (LogSetting::byDiscordGuildId($this->guildId)->get() as $setting) {
            $value = $setting->value ? "On" : "Off";
            $description .= "**{$setting->key}** = {$value}\n";
        }
        $embedBuilder->setDescription($description);

        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }
}
