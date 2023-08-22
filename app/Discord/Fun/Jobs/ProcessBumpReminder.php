<?php

namespace App\Discord\Fun\Jobs;

use App\Domain\Discord\Guild;
use Discord\Discord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessBumpReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Http::withHeaders(['Authorization' => "Bot " . config('discord.token')])->post(
            config('discord.api') . 'channels/' . $channelId . '/messages',
            ['content' => __('bot.bump-reminder', ['role' => "<@&{$roleId}>"])]
        );
    }
}
