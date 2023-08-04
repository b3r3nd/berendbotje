<?php

namespace App\Discord\Moderation\Commands\Timeout;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Moderation\Helpers\TimeoutHelper;
use App\Domain\Moderation\Models\Timeout;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

class Timeouts extends SlashIndexCommand
{
    public function permission(): Permission
    {
        return Permission::TIMEOUTS;
    }

    public function trigger(): string
    {
        return 'list';
    }

    public function __construct()
    {
        $this->description = __('bot.slash.timeouts');
        $this->slashCommandOptions = [
            [
                'name' => 'user_mention',
                'description' => __('bot.user-mention'),
                'type' => Option::USER,
                'required' => false,
            ],
        ];
        parent::__construct();
    }

    /**
     * @return Embed
     * @throws Exception
     */
    public function getEmbed(): Embed
    {
        $this->perPage = 5;
        if ($this->getOption('user_mention')) {
            $timeouts = Timeout::byGuild($this->guildId)->where(['discord_id' => $this->getOption('user_mention')])->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->orderBy('created_at', 'desc')->get();
            $this->total = Timeout::byGuild($this->guildId)->where(['discord_id' => $this->getOption('user_mention')])->count();
        } else {
            $timeouts = Timeout::byGuild($this->guildId)->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->orderBy('created_at', 'desc')->get();
            $this->total = Timeout::byGuild($this->guildId)->count();
        }

        $embedBuilder = EmbedBuilder::create($this, __('bot.timeout.title'));

        $embed = $embedBuilder->getEmbed();
        $description = __('bot.timeout.count', ['count' => $this->total]) . "\n\n";
        foreach ($timeouts as $timeout) {
            $description .= TimeoutHelper::timeoutLength($timeout);
        }
        $embed->setDescription($description);

        return $embed;
    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
