<?php

namespace App\Discord\Bump\Commands;

use App\Discord\Bump\Models\Bump;
use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Discord\Levels\Helpers\Helper;
use App\Discord\Roles\Enums\Permission;
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
        $this->total = Bump::byGuild($this->guildId)->count();

        $builder = EmbedBuilder::create($this)
            ->setTitle(__('bot.bump.title'))
            ->setFooter(__('bot.bump.footer'));

        if (strtolower($this->getOption('date-range')) === 'all-time') {
            foreach (Bump::byGuild($this->guildId)->groupBy('user_id')->orderBy('total', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->selectRaw('*, sum(count) as total')->get() as $index => $bumper) {
                $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
                $description .= "**{$bumper->user->tag()}** •  {$bumper->total}\n";
            }
            $builder->setDescription(__('bot.bump.description', ['bumpers' => $description]));

        } else {
            foreach (Bump::byGuild($this->guildId)
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
