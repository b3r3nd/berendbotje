<?php

namespace App\Discord\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\DiscordEvent;
use App\Discord\Core\Enums\Setting;
use App\Models\Emote;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use function Emoji\detect_emoji;

/**
 * It will only count emoji ONCE per message, te prevent people just spamming emotes 10x in a single message.
 */
class EmoteCounter extends DiscordEvent
{
    /**
     * @param Emote $emote
     * @return void
     */
    private function processEmote(Emote $emote): void
    {
        if (isset($emote->count)) {
            ++$emote->count;
        } else {
            $emote->count = 1;
        }
        $emote->save();
    }

    public function registerEvent()
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }
            if(!$message->guild_id) {
                return;
            }
            if (!$this->bot->getGuild($message->guild_id)?->getSetting(Setting::ENABLE_EMOTE)) {
                return;
            }

            // Checks for custom emotes
            if (preg_match('/<a?:.+?:\d+>/', $message->content, $matches)) {
                foreach ($matches as $match) {
                    $emoteInstance = Emote::firstOrCreate(['emote' => $match, 'guild_id' => \App\Models\Guild::get($message->guild_id)->id]);
                    $this->processEmote($emoteInstance);
                }
            }

            // Checks for default emotes
            $emotes = detect_emoji($message->content);
            if (!empty($emotes)) {
                $usedEmotes = [];
                foreach ($emotes as $emote) {
                    if (!in_array($emote['hex_str'], $usedEmotes, true)) {
                        $emoteInstance = Emote::firstOrCreate(['hex' => $emote['hex_str'], 'emote' => $emote['emoji'], 'guild_id' => \App\Models\Guild::get($message->guild_id)->id]);
                        $this->processEmote($emoteInstance);
                        $usedEmotes[] = $emote['hex_str'];
                    }
                }
            }
        });
    }
}
