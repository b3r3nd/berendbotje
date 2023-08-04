<?php

namespace App\Discord\Core\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashCommand;
use App\Domain\Discord\User;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Exception;

class UserSettings extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.userconfig');
        parent::__construct();
    }

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $embedBuilder = EmbedBuilder::create($this, __('bot.userconfig.title'));

        $user = User::get($this->interaction->member);

        if ($user->settings->isEmpty()) {
            $embedBuilder->setDescription(__('bot.userconfig.not-found'));
        } else {
            $description = "";
            foreach ($user->settings as $setting) {
                $description .= "**{$setting->key}** = {$setting->value} \n";
            }
            $embedBuilder->setDescription($description);
        }


        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed());
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
