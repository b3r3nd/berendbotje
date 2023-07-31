<?php

namespace App\Discord\Core\Events;

use App\Discord\Core\Models\DiscordUser;
use Database\Seeders\LogSettingsSeeder;
use Database\Seeders\MentionResponderSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingsSeeder;
use Discord\Discord;
use Discord\Parts\Guild\Guild;
use App\Discord\Core\Models\Guild as GuildModel;
use Discord\WebSockets\Event;
use Exception;

class GuildCreate extends DiscordEvent
{
    public function event(): string
    {
        return Event::GUILD_CREATE;
    }

    /**
     * @param object $guild
     * @param Discord $discord
     * @return void
     * @throws Exception
     */
    public function execute(object $guild, Discord $discord): void
    {
        if (!($guild instanceof Guild)) {
            return;
        }
        $guildModel = GuildModel::get($guild->id);
        if (!$guildModel) {
            $owner = DiscordUser::get($guild->owner_id);
            $guildModel = GuildModel::create([
                'owner_id' => $owner->id,
                'guild_id' => $guild->id,
            ]);

            // Use normal seeders to setup data
            (new SettingsSeeder())->processSettings($guildModel);
            (new LogSettingsSeeder())->processSettings($guildModel);
            $roleSeeder = new RoleSeeder();
            $roleSeeder->createAdminRole($guildModel, $owner);
            $roleSeeder->createModRole($guildModel);
            (new MentionResponderSeeder())->processMentionGroups($guildModel);

            $this->bot->addGuild($guildModel);
        }
    }
}
