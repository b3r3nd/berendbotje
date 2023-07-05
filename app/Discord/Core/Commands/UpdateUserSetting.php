<?php

namespace App\Discord\Core\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Models\DiscordUser;
use App\Discord\Core\Models\Setting;
use App\Discord\Core\Models\UserSetting;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

class UpdateUserSetting extends SlashCommand
{
    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'userset';
    }

    public function __construct()
    {
        $choices = [
            ['name' => "no_role_rewards", 'value' => "no_role_rewards"]
        ];

        $this->description = __('bot.slash.userset');
        $this->slashCommandOptions = [
            [
                'name' => 'setting_key',
                'description' => 'Key',
                'type' => Option::STRING,
                'required' => true,
                'choices' => $choices,
            ],
            [
                'name' => 'setting_value',
                'description' => 'Value',
                'type' => Option::STRING,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $key = $this->getOption('setting_key');
        $value = $this->getOption('setting_value');

        // When I add more settings I will change this :)
        if ($key !== 'no_role_rewards') {
            return EmbedFactory::failedEmbed($this, __('bot.set.not-exist', ['key' => $key]));
        }
        if (!is_numeric($value)) {
            return EmbedFactory::failedEmbed($this, __('bot.set.not-numeric', ['value' => $value]));
        }

        $user = DiscordUser::get($this->interaction->member->id);
        $guild = $this->bot->getGuild($this->guildId);

        $setting = $user->settings()->where('key', $key)->first();

        if ($setting) {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new UserSetting(['guild_id' => $guild->model->id, 'key' => $key, 'value' => $value]);
            $user->settings()->save($setting);
        }

        return EmbedFactory::successEmbed($this, __('bot.set.updated', ['key' => $key, 'value' => $value]));
    }
}
