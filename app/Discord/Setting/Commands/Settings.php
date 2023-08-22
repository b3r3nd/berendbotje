<?php

namespace App\Discord\Setting\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Permission\Enums\Permission;
use App\Domain\Setting\Models\Setting;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Exception;

class Settings extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::CONFIG;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.config');
        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->perPage = 15;
        $this->total = Setting::byDiscordGuildId($this->guildId)->count();
        $embedBuilder = EmbedBuilder::create($this, __('bot.set.title'));

        $description = "";
        foreach (Setting::byDiscordGuildId($this->guildId)->orderBy('key', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $setting) {
            $value = $setting->value;

            if (str_contains($setting->key, 'channel')) {
                $value = "<#{$setting->value}>";
            } elseif (str_contains($setting->key, 'role') && !str_contains($setting->key, 'rewards')) {
                $value = "<@&{$setting->value}>";
            } elseif (str_contains($setting->key, 'enable')) {
                $value = $setting->value ? "On" : "Off";
            }

            $description .= "**{$setting->key}** = {$value}\n";
        }
        $embedBuilder->setDescription($description);

        return $embedBuilder->getEmbed();
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
