<?php

namespace App\Discord\Admin\Commands;

use App\Discord\Core\SlashCommand;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Facades\Http;

class UpdateTopGG extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::CONFIG;
    }

    public function trigger(): string
    {
        return 'update';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.topgg');
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        Http::withHeaders(['Authorization' => config('discord.topgg-token')])
            ->post(config('discord.topgg-host') . 'bots/' . config('discord.topgg-id') . '/stats',
                ['server_count' => $this->discord->guilds->count()]
            );

        return MessageBuilder::new()->setContent("Updated listing to server count: " . $this->discord->guilds->count());
    }

    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
