<?php

namespace App\Discord\Moderation\Commands;

use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Moderation\Models\Timeout;
use App\Discord\Roles\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Exception;

class UpdateTimeoutReason extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::TIMEOUTS;
    }

    public function trigger(): string
    {
        return "edittimeout";
    }

    public function __construct()
    {
        $this->description = __('bot.slash.edit-timeout');
        parent::__construct();

        $this->slashCommandOptions = [
            [
                'name' => 'timeout_id',
                'description' => __('bot.timeout-id'),
                'type' => Option::INTEGER,
                'required' => true,
            ],
            [
                'name' => 'reason',
                'description' => __('bot.timeout-reason'),
                'type' => Option::STRING,
                'required' => true,
            ],
        ];

    }

    /**
     * @return MessageBuilder
     * @throws Exception
     */
    public function action(): MessageBuilder
    {
        $timeoutId = $this->getOption('timeout_id');
        $timeoutReason = $this->getOption('reason');
        $timeout = Timeout::find($timeoutId);

        if (!$timeout) {
            return EmbedFactory::failedEmbed($this, __('bot.timeout.not-found', ['id' => $timeoutId]));
        }

        $timeout->update(['reason' => $this->getOption('reason')]);
        return EmbedFactory::successEmbed($this, __('bot.timeout.updated', ['id' => $timeoutId, 'reason' => $timeoutReason]));
    }
}
