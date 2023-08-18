<?php

namespace App\Discord\Core\Providers;

use App\Discord\Core\Bot;
use App\Discord\Core\Events\GuildCreate;
use App\Discord\Core\Events\InteractionCreate;
use App\Discord\Core\Events\MessageCreate;
use App\Discord\Core\Events\VoiceStateUpdate;
use App\Discord\Core\Interfaces\ServiceProvider;
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

class EventServiceProvider implements ServiceProvider
{
    private array $messageEvents;
    private array $events;

    public function __construct()
    {
        $this->messageEvents = config('events.message');
        $this->events = config('events.events');
    }

    public function boot(Bot $bot): void
    {
        (new GuildCreate($bot))->register();
    }

    public function init(Bot $bot): void
    {
        foreach ($this->events as $class) {
            $instance = new $class($bot);
            $instance->register();
        }
        foreach ($this->messageEvents as $class) {
            $bot->addMessageAction(new $class());
        }
    }
}
