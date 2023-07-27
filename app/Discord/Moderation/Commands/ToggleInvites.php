<?php

namespace App\Discord\Moderation\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Interaction;

class ToggleInvites extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::INVITES;
    }

    public function trigger(): string
    {
        return 'toggle';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.toggle-invite');
        parent::__construct();
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): MessageBuilder
    {
        $guild = $this->interaction->guild;

        if (!$guild->feature_invites_disabled) {
            $guild->setFeatures(['INVITES_DISABLED' => true]);
            return EmbedFactory::successEmbed($this, __('bot.invites.disabled'));
        }

        $guild->setFeatures(['INVITES_DISABLED' => false]);
        return EmbedFactory::successEmbed($this, __('bot.invites.enabled'));
    }

    public function autoComplete(Interaction $interaction): array
    {
       return [];
    }
}
