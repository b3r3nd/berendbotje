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
    protected $signature = 'bot:run
    {--update : Update all slash commands}
    {--delete : Delete all slash commands}';

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
    public function handle()
    {
        if ($this->option('delete') && !$this->confirm('Are you sure you want do DELETE ALL slash commands?')) {
            $this->info("Quitting..");
            exit;
        }
        if ($this->option('update') && !$this->confirm('Are you sure you want do UPDATE/REGISTER ALL slash commands?')) {
            $this->info("Quitting..");
            exit;
        }
        $bot = new Bot($this->option('update'), $this->option('delete'));
        $bot->connect();
        return Command::SUCCESS;
    }
}
