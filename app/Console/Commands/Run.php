<?php

namespace App\Console\Commands;

use App\Discord\Core\Bot;
use Discord\Exceptions\IntentException;
use Illuminate\Console\Command;

class Run extends Command
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
    protected $description = 'Command to run the bot';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws IntentException
     */
    public function handle(): int
    {
        (new Bot())->connect();
        return Command::SUCCESS;
    }
}
