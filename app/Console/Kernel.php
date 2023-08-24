<?php

namespace App\Console;

use App\Discord\Levels\Helpers\Helper;
use App\Domain\Moderation\Models\Reminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->call(function () {
            foreach (Reminder::all() as $reminder) {
                if (!$reminder->executed_at) {
                    $executed = now()->subYear();
                } else {
                    $executed = Helper::parse($reminder->executed_at);
                }

                if (now()->diffInMinutes($executed) >= $reminder->interval) {
                    $url = config('discord.api') . "channels/{$reminder->channel}/messages";
                    $response = Http::withHeaders(['Authorization' => "Bot " . config('discord.token')])->post($url, ['content' => $reminder->message]);
                    $reminder->update(['executed_at' => now()]);
                }
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
