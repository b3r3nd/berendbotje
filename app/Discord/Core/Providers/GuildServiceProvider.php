<?php

namespace App\Discord\Core\Providers;

use App\Discord\Core\Bot;
use App\Discord\Core\Interfaces\ServiceProvider;
use App\Discord\Core\Models\Guild;
use Exception;

class GuildServiceProvider implements ServiceProvider
{

    public function boot(Bot $bot): void
    {
        // Silence is golden..
    }

    /**
     * @throws Exception
     */
    public function init(Bot $bot): void
    {
        foreach (Guild::all() as $guild) {
            if (!isset($this->guilds[$guild->guild_id])) {
                $bot->addGuild($guild);
            }
        }
    }
}
