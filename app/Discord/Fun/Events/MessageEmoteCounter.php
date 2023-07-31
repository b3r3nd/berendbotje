<?php

namespace App\Discord\Fun\Events;

use App\Discord\Core\Bot;
use App\Discord\Core\Enums\Setting;
use App\Discord\Core\Guild;
use App\Discord\Core\Interfaces\MessageCreateAction;
use App\Discord\Fun\Models\Emote;
use App\Models\Channel;
use Discord\Parts\Channel\Message;
use function Emoji\detect_emoji;

/**
 * It will only count emoji ONCE per message, te prevent people just spamming emotes 10x in a single message.
 */
class MessageEmoteCounter implements MessageCreateAction
{
    public function execute(Bot $bot, Guild $guildModel, Message $message, ?Channel $channel): void
    {
        if (!$guildModel->getSetting(Setting::ENABLE_EMOTE)) {
            return;
        }

        // Checks for custom emotes
        if (preg_match('/<a?:.+?:\d+>/', $message->content, $matches)) {
            foreach ($matches as $match) {
                $emoteInstance = Emote::firstOrCreate(['emote' => $match, 'guild_id' => \App\Discord\Core\Models\Guild::get($message->guild_id)->id]);
                $this->processEmote($emoteInstance);
            }
        }

        // Checks for default emotes
        $emotes = detect_emoji($message->content);
        if (!empty($emotes)) {
            $usedEmotes = [];
            foreach ($emotes as $emote) {
                if (!in_array($emote['hex_str'], $usedEmotes, true)) {
                    $emoteInstance = Emote::firstOrCreate(['hex' => $emote['hex_str'], 'emote' => $emote['emoji'], 'guild_id' => \App\Discord\Core\Models\Guild::get($message->guild_id)->id]);
                    $this->processEmote($emoteInstance);
                    $usedEmotes[] = $emote['hex_str'];
                }
            }
        }
    }

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
}
