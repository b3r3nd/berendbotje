<?php

namespace App\Jobs;

use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Intents;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Interaction $interaction;
    public Discord $discord;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
      //
    }

    /**
     * @return void
     * @throws IntentException
     */
    public function handle(): void
    {
        $this->discord = new Discord(['token' => config('discord.token'), 'intents' => Intents::GUILDS]);
        $this->discord->on('ready', function (Discord $discord) {
            $channel = $discord->getChannel(1030402514786459718);
            $channel?->sendMessage("EZ PZ")->done(function () {
                $this->discord->close();
            });
        });
        $this->discord->run();


    }
}
