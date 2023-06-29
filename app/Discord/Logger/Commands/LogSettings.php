<?php

namespace App\Discord\Logger\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Discord\Logger\Models\LogSetting;
use App\Discord\Roles\Enums\Permission;
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
        $embedBuilder = EmbedBuilder::create($this->discord)
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
