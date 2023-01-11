<?php

namespace App\Jobs;

use App\Models\Guild;
use Discord\Discord;
use Discord\WebSockets\Intents;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBumpReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Discord $discord;
    public string $guildId;

    public function __construct(string $guildId)
    {
        $this->guildId = $guildId;
    }

    public function handle(): void
    {
        $guild = Guild::get($this->guildId);
        $roleId = $guild->settings()->where('key', 'bump_reminder_role')->first()->value;
        $channelId = $guild->settings()->where('key', 'bump_channel')->first()->value;
        $this->discord = new Discord(['token' => config('discord.token'), 'intents' => Intents::GUILDS]);

        $this->discord->on('ready', function (Discord $discord) use ($roleId, $channelId) {
            $channel = $discord->getChannel($channelId);
            $channel?->sendMessage(__('bot.bump-reminder', ['role' => "<@&{$roleId}>"]))->done(function () {
                $this->discord->close();
            });
        });
        $this->discord->run();
    }
}
