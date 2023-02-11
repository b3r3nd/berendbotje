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

class ProcessImageGenration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Discord $discord;
    public string $channelId;
    public string $prompt;

    public function __construct(string $channelId, string $prompt)
    {

        $this->channelId = $channelId;
        $this->prompt = $prompt;
    }

    public function handle(): void
    {
        $response = \Http::withToken(env('OPENAI_API_KEY'))
            ->post("https://api.openai.com/v1/images/generations",
                [
                    'prompt' => $this->prompt,
                    'n' => 1,
                    'size' => '256x256',
                    'response_format' => 'url',
                ]);

        $imageString = "";
        foreach ($response->json()['data'] as $images) {
            foreach ($images as $imageUrl) {
                $imageString .= $imageUrl;
            }
        }


        $this->discord = new Discord(['token' => config('discord.token'), 'intents' => Intents::GUILDS]);
        $this->discord->on('ready', function (Discord $discord) use ($imageString) {
            $channel = $discord->getChannel($this->channelId);
            $channel?->sendMessage($imageString)->done(function () {
                $this->discord->close();
            });
        });
        $this->discord->run();
    }

}
