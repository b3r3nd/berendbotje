<?php

namespace App\Discord\Statistics;

use App\Discord\Core\Bot;
use App\Models\Emote;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use function Emoji\detect_emoji;

/**
 * @TODO This code will consume a shit ton of resources looping over emotes and executing DB queries by each emote, improve.
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
            if (preg_match('/<a?:.+?:\d+>/', $message->content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches as $match) {
                    $emote = Emote::firstOrCreate(['emote' => $match[0]]);
                    $this->processEmote($emote);
                }
            }

            // Checks for default emotes
            $emotes = detect_emoji($message->content);
            if (empty(!$emotes)) {
                foreach ($emotes as $emoteS) {
                    $emote = Emote::firstOrCreate(['hex' => $emoteS['hex_str'], 'emote' => $emoteS['emoji']]);
                    $this->processEmote($emote);
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
