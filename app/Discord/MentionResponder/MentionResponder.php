<?php

namespace App\Discord\MentionResponder;

use App\Discord\Core\Bot;
use App\Domain\Discord\User;
use App\Domain\Fun\Models\MentionGroup;
use App\Domain\Moderation\Models\Timeout;
use App\Domain\Setting\Enums\Setting as SettingEnum;
use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Exception;

/**
 * @property Bot $bot               Bot the event belongs to
 * @property Discord $discord       Easy to access discord instance
 * @property string $guildId        Discord ID for the guild this responder belongs to
 * @property int $guildModelId      Model ID for the guild this responder belongs to
 * @property array $roleReplies     List of mention replies for this guild which require a certain role
 * @property array $noRoleReplies   List of mention replies for this guild which require NOT to have a certain role
 * @property array $lastResponses   List of responses recently used (60 sec) so no duplicates are send
 * @property array $userReplies     List of mention replies for this guild for specific users
 * @property array $lastMessages    List of last replies for each user to determine cooldowns
 */
class MentionResponder
{
    private Discord $discord;
    private Bot $bot;
    private string $guildId;
    private int $guildModelId;
    private array $roleReplies = [];
    private array $noRoleReplies = [];
    private array $userReplies = [];
    private array $lastMessages = [];

    /**
     * @throws Exception
     */
    public function __construct(string $guildId, Bot $bot)
    {
        $this->discord = $bot->discord;
        $this->bot = $bot;
        $this->guildId = $guildId;
        $this->guildModelId = \App\Domain\Discord\Guild::get($guildId)->id;
        $this->loadReplies();
        $this->registerMentionResponder();
    }

    /**
     * @return void
     */
    public function loadReplies(): void
    {
        $this->roleReplies = [];
        $this->noRoleReplies = [];
        foreach (MentionGroup::byGuild($this->guildId)->get() as $mentionGroup) {

            $replies = [];
            foreach ($mentionGroup->replies as $reply) {
                if ($mentionGroup->multiplier > 1) {
                    $replies = array_merge($replies, array_fill(0, $mentionGroup->multiplier, $reply->reply));
                } else {
                    $replies[] = $reply->reply;
                }
            }

            if ($mentionGroup->has_role) {
                $this->roleReplies[$mentionGroup->name] = $replies;
            } elseif ($mentionGroup->has_user) {
                $this->userReplies[$mentionGroup->name] = $replies;
            } else {
                $this->noRoleReplies[$mentionGroup->name] = $replies;
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function registerMentionResponder(): void
    {
        $this->discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
            if ($message->author->bot || !$message->guild_id || $message->guild_id !== $this->guildId ||
                !str_contains($message->content, $discord->user->id) ||
                !$this->bot->getGuild($this->guildId)?->getSetting(SettingEnum::ENABLE_MENTION_RESPONDER)) {
                return;
            }

            if (str_contains($message->content, '?give')) {
                $message->reply('Thanks! 😎');
                return;
            }

            if (isset($this->lastMessages[$message->author->id])) {
                $messages = $this->lastMessages[$message->author->id];
                $now = Carbon::now();

                foreach ($messages as $index => $lastMessage) {
                    if ($now->diffInSeconds($lastMessage) > 60) {
                        unset($this->lastMessages[$message->author->id][$index]);
                    }
                }

                $messages = $this->lastMessages[$message->author->id];
                if (count($messages) === 5) {
                    $message->reply($this->getRandom($this->roleReplies['Blocked'] ?? []));
                    $this->lastMessages[$message->author->id][] = Carbon::now();
                    return;
                }

                if (count($messages) >= 6) {
                    return;
                }

                if (count($messages) >= 3) {
                    $message->reply($this->getRandom($this->roleReplies['Annoyed'] ?? []));
                    $this->lastMessages[$message->author->id][] = Carbon::now();
                    return;
                }
            }


            $roles = collect($message->member->roles);
            $responses = [];

            foreach ($this->roleReplies as $group => $replies) {
                if (is_int($group) && $roles->contains('id', $group)) {
                    $responses = array_merge($responses, $replies);
                }
            }

            foreach ($this->noRoleReplies as $group => $replies) {
                if (is_int($group) && !$roles->contains('id', $group)) {
                    $responses = array_merge($responses, $replies);
                }
            }

            foreach ($this->userReplies as $user => $replies) {
                if (is_int($user) && $message->author->id == $user) {
                    $responses = array_merge($responses, $replies);
                }
            }

            $discordUser = User::get($message->author->id);
            $cringeCounter = $discordUser->cringeCounters()->where('guild_id', $this->guildModelId)->get()->first()->count ?? 0;
            $bumpCounter = $discordUser->bumpCounters()->where('guild_id', $this->guildModelId)->selectRaw('*, sum(count) as total')->first();
            $timeoutCounter = Timeout::byGuild($message->guild_id)->where(['discord_id' => $message->author->id])->count();

            if ($bumpCounter->total > 100) {
                $responses = array_merge($responses, $this->roleReplies['BumpCounter'] ?? []);
            }
            if ($timeoutCounter > 1) {
                $responses = array_merge($responses, $this->roleReplies['Muted'] ?? []);
            }
            if ($cringeCounter > 10) {
                $responses = array_merge($responses, $this->roleReplies['CringeCounter'] ?? []);
            }

            // Replies for everyone
            $responses = array_merge($responses, $this->roleReplies['Default'] ?? []);


            if (!empty($responses)) {
                $message->reply($this->getRandom($responses));
                $this->lastMessages[$message->author->id][] = Carbon::now();
            }
        });
    }

    /**
     * @param array $array
     * @return mixed
     * @throws Exception
     */
    private function getRandom(array $array): mixed
    {
        return $array[random_int(0, (count($array) - 1))];
    }

}
