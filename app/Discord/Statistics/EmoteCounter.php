<?php

namespace App\Discord\Statistics;

use App\Discord\Core\Bot;
use App\Models\Emote;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use function Emoji\detect_emoji;

/**
 * It will only count emoji ONCE per message, te prevent people just spamming emotes 10x in a single message.
 */
class EmoteCounter
{
    public function __construct()
    {
        Bot::getDiscord()->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot) {
                return;
            }

            // Checks for custom emotes
            if (preg_match('/<a?:.+?:\d+>/', $message->content, $matches)) {
                foreach ($matches as $match) {
                    $emoteInstance = Emote::firstOrCreate(['emote' => $match[0]]);
                    $this->processEmote($emoteInstance);
                }
            }

            // Checks for default emotes
            $emotes = detect_emoji($message->content);
            if (empty(!$emotes)) {
                $usedEmotes = [];
                foreach ($emotes as $emote) {
                    if (!in_array($emote['hex_str'], $usedEmotes)) {
                        $emoteInstance = Emote::firstOrCreate(['hex' => $emote['hex_str'], 'emote' => $emote['emoji']]);
                        $this->processEmote($emoteInstance);
                        $usedEmotes[] = $emote['hex_str'];
                    }
                }
            }
        });
    }

    /**
     * @param Emote $emote
     * @return void
     */
    private function processEmote(Emote $emote): void
    {
        if (isset($emote->count)) {
            $emote->count = $emote->count + 1;
        } else {
            $emote->count = 1;
        }
        $emote->save();
    }
}
