<?php

namespace App\Discord\Fun;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashCommand;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Illuminate\Support\Facades\Http;

class UrbanDictionary extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'urb';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.urb');
        $this->slashCommandOptions = [
            [
                'name' => 'search_term',
                'description' => 'Search term',
                'type' => Option::STRING,
                'required' => true,
            ],
        ];
        parent::__construct();
    }

    public function action(): MessageBuilder
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => config('discord.urb-token'),
            'X-RapidAPI-Host' => config('discord.urb-host'),
        ])->get("https://". config('discord.urb-host') . "/define", ['term' => $this->getOption('search_term')]);


        if (empty($response->json()['list'])) {
             return EmbedFactory::failedEmbed($this->discord, __('bot.no-valid-term', ['term' => $this->getOption('search_term')]));
        }

        return MessageBuilder::new()->addEmbed(EmbedBuilder::create($this->discord)
            ->setFooter($response->json()['list'][0]['permalink'])
            ->setTitle($this->getOption('search_term'))
            ->setDescription($response->json()['list'][0]['definition'])->getEmbed());
    }
}
