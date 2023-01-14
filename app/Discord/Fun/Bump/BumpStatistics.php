<?php

namespace App\Discord\Fun\Bump;

use App\Discord\Core\Bot;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Enums\Permission;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Helper;
use App\Models\Bumper;
use Carbon\Carbon;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;

class BumpStatistics extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'bumpstats';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.bumpstats');

        $this->slashCommandOptions = [
            [
                'name' => 'date-range',
                'description' => 'Date range',
                'type' => Option::STRING,
                'required' => true,
                'choices' => [
                    ['name' => 'Monthly', 'value' => 'monthly'],
                    ['name' => 'All Time', 'value' => 'all-time'],
                ]
            ],
        ];

        parent::__construct();
    }


    public function getEmbed(): Embed
    {
        $description = "";
        $this->total = Bumper::byGuild($this->guildId)->count();

        $builder = EmbedBuilder::create(Bot::get()->discord())
            ->setTitle(__('bot.bump.title'))
            ->setFooter(__('bot.bump.footer'));

        if (strtolower($this->arguments[0]) === 'all-time') {
            foreach (Bumper::byGuild($this->guildId)->groupBy('user_id')->orderBy('total', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->selectRaw('*, sum(count) as total')->get() as $index => $bumper) {
                $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
                $description .= "**{$bumper->user->tag()}** •  {$bumper->total}\n";
            }
            $builder->setDescription(__('bot.bump.description', ['bumpers' => $description]));

        } else {
            foreach (Bumper::byGuild($this->guildId)
                         ->whereMonth('created_at', date('m'))
                         ->groupBy('user_id')
                         ->orderBy('total', 'desc')
                         ->skip($this->getOffset($this->getLastUser()))
                         ->limit($this->perPage)
                         ->selectRaw('*, sum(count) as total')
                         ->get() as $index => $bumper) {

                $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
                $description .= "**{$bumper->user->tag()}** •  {$bumper->total}\n";
            }
            $builder->setDescription(__('bot.bump.description-month', ['bumpers' => $description]));
        }

        return $builder->getEmbed();

    }
}
