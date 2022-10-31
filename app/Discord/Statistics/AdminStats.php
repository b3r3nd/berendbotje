<?php

namespace App\Discord\Statistics;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\SlashAndMessageCommand;
use Discord\Builders\MessageBuilder;

class AdminStats extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'adminstats';
    }

    public function action(): MessageBuilder
    {
        // TODO: Implement action() method.
    }
}
