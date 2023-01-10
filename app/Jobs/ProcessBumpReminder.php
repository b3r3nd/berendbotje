<?php

namespace App\Jobs;

use App\Discord\Core\Bot;
use App\Models\Guild;
use Discord\Discord;
use Discord\Parts\User\Activity;
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
        //sleep(30);

        $guild = Guild::get($this->guildId);
        $roleId = $guild->settings()->where('key', 'bump_reminder_role')->first()->value;
        $channelId = $guild->settings()->where('key', 'bump_channel')->first()->value;

        $this->discord = new Discord([
                'token' => config('discord.token'),
                'loadAllMembers' => true,
                'storeMessages' => true,
                'intents' => Intents::getDefaultIntents() | Intents::GUILD_VOICE_STATES | Intents::GUILD_MEMBERS |
                    Intents::MESSAGE_CONTENT | Intents::GUILDS | Intents::GUILD_INVITES | Intents::GUILD_EMOJIS_AND_STICKERS
            ]
        );

        $this->discord->on('ready', function (Discord $discord) use ($roleId, $channelId) {
            $channel = $discord->getChannel($channelId);
            $channel?->sendMessage("11111");
            $channel?->sendMessage("BUMP TIME!!!! <@&{$roleId}>");
            $channel?->sendMessage("22222");
            sleep(5);
            $this->discord->close();
        });

        $this->discord->run();
    }
}
