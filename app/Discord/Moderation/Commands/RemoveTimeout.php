<?php

namespace App\Discord\Moderation\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Moderation\Models\Timeout;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;

class RemoveTimeout extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::TIMEOUTS;
    }

    public function trigger(): string
    {
        return 'removetimeout';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.remove-timeout');
        parent::__construct();

        $this->slashCommandOptions = [
            [
                'name' => 'timeout_id',
                'description' => __('bot.timeout-id'),
                'type' => Option::INTEGER,
                'required' => true,
            ],
        ];

    }

    public function action(): MessageBuilder
    {
        $timeoutId = $this->getOption('timeout_id');
        $timeout = Timeout::find($timeoutId);

        if (!$timeout) {
            return EmbedFactory::failedEmbed($this, __('bot.timeout.not-found', ['id' => $timeoutId]));
        }

        $timeout->delete();
        return EmbedFactory::successEmbed($this, __('bot.timeout.deleted', ['id' => $timeoutId]));
    }
}
