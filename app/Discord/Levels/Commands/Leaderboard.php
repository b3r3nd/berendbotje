<?php

namespace App\Discord\Levels\Commands;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\SlashIndexCommand;
use App\Domain\Fun\Helpers\Helper;
use App\Domain\Fun\Models\UserXP;
use App\Domain\Permission\Enums\Permission;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Exception;

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
        $this->description = __('bot.slash.leaderboard');
        $this->slashCommandOptions = [
            [
                'name' => 'type',
                'description' => __('bot.type'),
                'type' => Option::STRING,
                'required' => true,
                'choices' => [
                    ['name' => __('bot.lxp'), 'value' => 'xp'],
                    ['name' => __('bot.lvoice'), 'value' => 'voice'],
                    ['name' => __('bot.lmsg'), 'value' => 'msg'],
                ]
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
        $this->total = UserXP::byGuild($this->guildId)->count();
        $description = "";

        if (strtolower($this->getOption('type')) === 'voice') {
            foreach (UserXP::byGuild($this->guildId)->orderBy('voice_seconds', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $index => $messageCounter) {
                $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
                $voice = Helper::getVoiceLabel($messageCounter->voice_seconds);
                $description .= "Level **{$messageCounter->level}** • {$messageCounter->user->tag()} • {$voice} \n";
            }
            return EmbedBuilder::create($this, __('bot.messages.voice-title'), __('bot.messages.description', ['users' => $description]))->getEmbed();
        }
        if (strtolower($this->getOption('type')) === 'msg') {
            foreach (UserXP::byGuild($this->guildId)->orderBy('count', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $index => $messageCounter) {
                $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
                $description .= "Level **{$messageCounter->level}** • {$messageCounter->user->tag()} • {$messageCounter->count} \n";
            }
            return EmbedBuilder::create($this, __('bot.messages.msg-title'), __('bot.messages.description', ['users' => $description]))->getEmbed();
        }

        foreach (UserXP::byGuild($this->guildId)->orderBy('xp', 'desc')->skip($this->getOffset($this->getLastUser()))->limit($this->perPage)->get() as $index => $messageCounter) {
            $description .= Helper::indexPrefix($index, $this->getOffset($this->getLastUser()));
            $xp = Helper::format($messageCounter->xp);
            $description .= "Level **{$messageCounter->level}** • {$messageCounter->user->tag()} • {$xp} xp \n";
        }
        return EmbedBuilder::create($this, __('bot.messages.xp-title'), __('bot.messages.description', ['users' => $description]))->getEmbed();

    }

    /**
     * @param Interaction $interaction
     * @return array
     */
    public
    function autoComplete(Interaction $interaction): array
    {
        return [];
    }
}
