<?php

namespace App\Discord\Moderation\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\SlashCommand;
use App\Discord\Moderation\Jobs\ProcessRoles;
use App\Domain\Permission\Enums\Permission;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class GiveRole extends SlashCommand
{

    public function permission(): Permission
    {
        return Permission::CONFIG;
    }

    public function trigger(): string
    {
        return 'give';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.give-role');
        $this->slashCommandOptions = [
            [
                'name' => 'date',
                'description' => __('bot.date'),
                'type' => Option::STRING,
                'required' => true,
            ],
            [
                'name' => 'role',
                'description' => __('bot.role'),
                'type' => Option::ROLE,
                'required' => true,
            ],
        ];
        parent::__construct();
    }


    public function action(): MessageBuilder
    {
        try {
            $date = Carbon::parse($this->getOption('date'));
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            return EmbedFactory::failedEmbed($this, __('bot.invalid-date'));
        }

        ProcessRoles::dispatch($this->guildId, $this->getOption('role'), $date->toDate());
        return EmbedFactory::successEmbed($this, __('bot.process', ['date' => $date->toDate()->format('d-m-Y'), 'role' => $this->getOption('role')]));
    }


    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
