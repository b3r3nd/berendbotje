<?php

namespace App\Discord\Core\Events;

use App\Discord\Core\Builders\EmbedBuilder;
use App\Discord\Core\Builders\EmbedFactory;
use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Interfaces\Events\GUILD_CREATE;
use App\Domain\Discord\Guild as GuildModel;
use App\Domain\Discord\User;
use App\Domain\Setting\Enums\Setting;
use Database\Seeders\LogSettingsSeeder;
use Database\Seeders\MentionResponderSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingsSeeder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Guild\Guild;
use Discord\WebSockets\Event;
use Exception;

class GuildCreate extends DiscordEvent implements GUILD_CREATE
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
            $owner = User::get($guild->owner_id);
            $guildModel = GuildModel::create([
                'owner_id' => $owner->id,
                'guild_id' => $guild->id,
                'name' => $guild->name,
            ]);

            // Use normal seeders to setup data
            (new SettingsSeeder())->processSettings($guildModel);
            (new LogSettingsSeeder())->processSettings($guildModel);
            $roleSeeder = new RoleSeeder();
            $roleSeeder->createAdminRole($guildModel, $owner);
            $roleSeeder->createModRole($guildModel);
            (new MentionResponderSeeder())->processMentionGroups($guildModel);

            $this->bot->addGuild($guildModel);

            /**
             * Temp hardcoded log to keep track of server count until we reach verification
             */
            $count = \App\Domain\Discord\Guild::all()->count();
            $embed = EmbedBuilder::createForLog($this->bot->discord);
            $embed->setTitle("Joined new guild");
            $embed->setDescription("**Name**: {$guild->name} \n **ID**: {$guild->id} \n **Owner**: {$owner->tag()} \n **Total Guilds: {$count}");
            $this->bot->discord->getChannel(1121480252829470781)?->sendMessage(MessageBuilder::new()->addEmbed($embed));
        }
    }
}
