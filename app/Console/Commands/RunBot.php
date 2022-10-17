<?php

namespace App\Console\Commands;

use App\Discord\Core\Bot;
use Illuminate\Console\Command;

class RunBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SimpleCommand description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $discord = Bot::setup();
        $discord->run();
        return Command::SUCCESS;
    }
}
