<?php

namespace App\Jobs;

use App\Discord\Fun\Music\MusicPlayer;
use App\Models\Song;
use Discord\Parts\Channel\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessYoutubeDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $youtubeUrl;
    public Channel $channel;
    public MusicPlayer $musicPlayer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $youtubeUrl, MusicPlayer $musicPlayer)
    {
        $this->youtubeUrl = $youtubeUrl;
        $this->musicPlayer = $musicPlayer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $filename = Str::random(10) . ".mp3";
        $tmpFileLocation = Storage::path($filename);
        system("youtube-dl -x -o {$tmpFileLocation} --audio-format mp3 {$this->youtubeUrl} --ignore-errors");

        Song::create([
            'youtube_url' => $this->youtubeUrl,
            'filename' => $filename,
            'queue_order' => 0
        ]);
    }
}
