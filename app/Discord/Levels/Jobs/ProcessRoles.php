<?php

namespace App\Discord\Levels\Jobs;

use App\Domain\Moderation\Helpers\DurationHelper;
use App\Domain\Moderation\Models\RoleReward;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessRoles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $guildId;

    public function __construct(string $guildId)
    {
        $this->guildId = $guildId;
    }

    /**
     * @param int $chunk
     * @param $after
     * @return void
     *
     * @see https://discord.com/developers/docs/resources/guild#list-guild-members
     */
    public function processMembers(int $chunk, $after = null): void
    {
        $url = config('discord.api') . "guilds/{$this->guildId}/members?limit={$chunk}";
        // if we get 0 from the api it means there are no more members to process
        if ($after === 0) {
            return;
        }
        if ($after) {
            $url .= "&after={$after}";
        }
        $response = Http::withHeaders(['Authorization' => "Bot " . config('discord.token')])->get($url);
        if ($response->status() === 429) {
            $result = $response->json();
            sleep($result['retry_after']);
            $this->processMembers($chunk, $after);
            return;
        }
        $next = 0;
        $rewards = RoleReward::duration($this->guildId)->get();
        foreach ($response->json() as $member) {
            $joinedAt = DurationHelper::parse($member['joined_at']);


            foreach ($rewards as $reward) {
                if ($reward->duration) {
                    $matches = DurationHelper::match($reward->duration);
                    $date = DurationHelper::getDate($matches);
                    if ($joinedAt->lt($date)) {
                        $this->giveRole($member['user']['id'], $reward->role);
                    } else if (in_array($reward->role, $member['roles'], true)) {
                        $this->removeRole($member['user']['id'], $reward->role);
                    }
                }
            }


            $next = $member['user']['id'];
        }
        $this->processMembers($chunk, $next);
    }

    /**
     * @param $userId
     * @param $roleId
     * @return void
     *
     * @see https://discord.com/developers/docs/resources/guild#add-guild-member-role
     */
    public function giveRole($userId, $roleId): void
    {
        $url = config('discord.api') . "guilds/{$this->guildId}/members/{$userId}/roles/{$roleId}";
        $response = Http::withHeaders(['Authorization' => "Bot " . config('discord.token')])->put($url);

        if ($response->status() === 429) {
            $result = $response->json();
            sleep($result['retry_after']);
            $this->giveRole($userId, $roleId);
        }
    }

    /**
     * @param $userId
     * @param $roleId
     * @return void
     *
     * @see https://discord.com/developers/docs/resources/guild#remove-guild-member-role
     */
    public function removeRole($userId, $roleId): void
    {
        $url = config('discord.api') . "guilds/{$this->guildId}/members/{$userId}/roles/{$roleId}";
        $response = Http::withHeaders(['Authorization' => "Bot " . config('discord.token')])->delete($url);

        if ($response->status() === 429) {
            $result = $response->json();
            sleep($result['retry_after']);
            $this->removeRole($userId, $roleId);
        }
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->processMembers(1000);
    }

}
