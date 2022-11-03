<?php

namespace App\Discord;

use App\Discord\Core\AccessLevels;
use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\EmbedFactory;
use App\Models\Admin;
use App\Models\DiscordUser;
use App\Models\Setting;
use Discord\Http\Exceptions\NoPermissionsException;

class SetupServer extends MessageCommand
{

    public function accessLevel(): AccessLevels
    {
        return AccessLevels::GOD;
    }

    public function trigger(): string
    {
        return 'addserver';
    }

    public function __construct()
    {
        parent::__construct();
        $this->requiredArguments = 2;
        $this->usageString = __('bot.server.usage-addserver');
    }

    /**
     * @throws NoPermissionsException
     */
    public function action(): void
    {
        $user = DiscordUser::getByGuild($this->arguments[1], $this->arguments[0]);
        $user->admin()->save(new Admin(['level' => 1000]));
        Setting::create(['key' => 'xp_count', 'value' => 15, 'guild_id' => $this->arguments[0]]);
        Setting::create(['key' => 'xp_cooldown', 'value' => 60, 'guild_id' => $this->arguments[0]]);

        $this->message->channel->sendMessage(EmbedFactory::successEmbed(__('bot.server.added', ['id' => $this->arguments[0], 'owner' => $user->tag()])));

    }
}
