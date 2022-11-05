<?php

namespace App\Discord\Fun;

use App\Discord\Core\Bot;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedBuilder;
use App\Discord\Core\EmbedFactory;
use Illuminate\Support\Facades\Http;

class UrbanDictionary extends MessageCommand
{

    public function permission(): string
    {
        return "";
    }

    public function trigger(): string
    {
        return 'urb';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 1;
        $this->usageString = __('bot.no-term');
    }

    public function action(): void
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => env('URB_TOKEN'),
            'X-RapidAPI-Host' => env('URB_HOST'),
        ])->get('https://mashape-community-urban-dictionary.p.rapidapi.com/define', ['term' => $this->messageString]);


        if (empty($response->json()['list'])) {
            $this->message->channel->sendMessage(EmbedFactory::failedEmbed(__('bot.no-valid-term', ['term' => $this->messageString])));
            return;
        }
        $embedBuilder = EmbedBuilder::create(Bot::getDiscord())
            ->setFooter($response->json()['list'][0]['permalink'])
            ->setTitle($this->messageString)
            ->setDescription($response->json()['list'][0]['definition']);

        $this->message->channel->sendEmbed($embedBuilder->getEmbed());

    }
}
