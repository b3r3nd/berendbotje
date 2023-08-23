<?php

namespace App\Discord\Moderation\Jobs;

use Discord\Discord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ProcessRoles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $guildId;
    private int $roleId;
    private \DateTime $dateTime;

    public function __construct(string $guildId, int $roleId, \DateTime $dateTime)
    {

        $this->guildId = $guildId;
        $this->roleId = $roleId;
        $this->dateTime = $dateTime;
    }


    public function processMembers(int $chunk, \DateTime $date, $after = null,): void
    {
        $url = config('discord.api') . "guilds/{$this->guildId}/members?limit={$chunk}";

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
            $this->processMembers($chunk, $date, $after);
            return;
        }

        $next = 0;
        foreach ($response->json() as $member) {
            try {
                $joinedAt = Carbon::parse($member['joined_at']);
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                return;
            }

            if ($joinedAt->lt($date)) {
                $this->giveRole($member['user']['id'], $this->roleId);
            }
            $next = $member['user']['id'];
        }

        $this->processMembers($chunk, $date, $next);
    }

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

    public function handle(): void
    {
        $this->processMembers(5, $this->dateTime);
    }

}
