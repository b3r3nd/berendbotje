<?php

namespace App\Discord\Fun;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\SlashAndMessageCommand;
use Discord\Builders\MessageBuilder;
use Illuminate\Support\Facades\Http;

class Ask extends SlashAndMessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::NONE;
    }

    public function trigger(): string
    {
        return 'ask';
    }

    public function action(): MessageBuilder
    {
        $response = Http::get('https://yesno.wtf/api');
        return MessageBuilder::new()->setContent($response->json('image'));
    }
}
