<?php

use App\Discord\Core\Events\InteractionCreate;
use App\Discord\Core\Events\MessageCreate;
use App\Discord\Core\Events\VoiceStateUpdate;
use App\Discord\Fun\Events\BanKickCounter;
use App\Discord\Fun\Events\BumpCounter;
use App\Discord\Fun\Events\MessageCount;
use App\Discord\Fun\Events\MessageEmoteCounter;
use App\Discord\Fun\Events\MessageReact;
use App\Discord\Fun\Events\MessageReminder;
use App\Discord\Levels\Events\MessageXpCounter;
use App\Discord\Levels\Events\VoiceXpCounter;
use App\Discord\Logger\Events\DMLogger;
use App\Discord\Logger\Events\GuildBanRemove;
use App\Discord\Logger\Events\GuildMemberAdd;
use App\Discord\Logger\Events\GuildMemberRemove;
use App\Discord\Logger\Events\GuildMemberUpdate;
use App\Discord\Logger\Events\InviteCreate;
use App\Discord\Logger\Events\InviteDelete;
use App\Discord\Logger\Events\MessageDelete;
use App\Discord\Logger\Events\MessageUpdate;
use App\Discord\Logger\Events\VoiceStateLogger;
use App\Discord\Message\Events\MessageCommandResponse;
use App\Discord\Message\Events\WelcomeUser;
use App\Discord\Moderation\Events\DetectTimeout;
use App\Discord\Moderation\Events\MessageMediaFilter;
use App\Discord\Moderation\Events\MessageStickerFilter;

return [
    /*
    |--------------------------------------------------------------------------
    | Discord Events
    |--------------------------------------------------------------------------
    |
    | Discord Events are for example GUILD_CREATE or INTERACTION_CREATE when
    | adding new events always extend the DiscordEvent class and implement
    | the interface required for that specific Event. For example when you
    | Add an event for MESSAGE_DELETE, you implement the MESSAGE_DELETE
    | interface.
    |
    */
    'events' => [
        InteractionCreate::class,
        MessageCreate::class,
        VoiceStateUpdate::class,
        VoiceXpCounter::class,
        VoiceStateLogger::class,
        DetectTimeout::class,
        WelcomeUser::class,
        BanKickCounter::class,
        GuildBanRemove::class,
        GuildMemberAdd::class,
        GuildMemberRemove::class,
        GuildMemberUpdate::class,
        InviteCreate::class,
        InviteDelete::class,
        MessageDelete::class,
        MessageUpdate::class,
        DMLogger::class,
        BumpCounter::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Message Events
    |--------------------------------------------------------------------------
    |
    | Message Events trigger on MESSAGE_CREATE, because we have a lot of them
    | we use a kind of wrapper to prevent a lot of duplicate stuff
    | make sure to implement the MessageCreateAction
    |
    */
    'message' => $messageEvents = [
        MessageXpCounter::class,
        MessageMediaFilter::class,
        MessageStickerFilter::class,
        MessageCount::class,
        MessageReact::class,
        MessageCommandResponse::class,
        MessageEmoteCounter::class,
        MessageReminder::class,
    ],
];
