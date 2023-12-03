<?php

namespace App\Discord\Fun\Commands\Count;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Fun\Helpers\Helper;
use App\Domain\Fun\Models\UserCounter;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;

class Leaderboard extends SlashIndexCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'leaderboard';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.countleaderboard');
        $this->slashCommandOptions = [
            [
                'name' => 'type',
                'description' => __('bot.type'),
                'type' => Option::STRING,
                'required' => true,
                'choices' => [
                    ['name' => __('bot.total'), 'value' => 'total'],
                    ['name' => __('bot.highest'), 'value' => 'highest'],
                    ['name' => __('bot.fails'), 'value' => 'fails'],
                ]
            ],
        ];
        parent::__construct();
    }

    public function setDesc($user, $index, $value)
    {
        $description = Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
        $description .= "{$value} • {$user}\n";

        return $description;
    }

    /**
     * @param $key
     * @return string
     */
    public function leaderboard($key): string
    {
        $description = "";
        foreach (UserCounter::byGuild($this->guildId)->orderBy($key, 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $index => $counter) {
            $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
            $description .= "{$counter->{$key}} • {$counter->user->tag()}\n";
        }

        return $description;
    }

    public function getEmbed(): Embed
    {
        $this->total = UserCounter::byGuild($this->guildId)->count();

        if (strtolower($this->getOption('type')) === 'highest') {
            $title = 'Highest Count';
            $description = $this->leaderboard('highest_count');

        } else if (strtolower($this->getOption('type')) === 'fails') {
            $title = 'Most Fails';
            $description = $this->leaderboard('fail_count');
        } else {
            $title = 'Total Count';
            $description = $this->leaderboard('count');
        }

        return EmbedBuilder::create($this, $title, $description)->getEmbed();

    }


    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }

}
