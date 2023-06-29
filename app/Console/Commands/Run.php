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
    protected $signature = 'bb:run
    {--delcmd : Deletes all slash commands}
    {--updatecmd : Also updates all slash commands}
    {--dev : Run only development comands}';

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
        $this->info("========================");
        $this->info("Running bot...");
        $this->info("Dev mode: {$this->option('dev')}");
        $this->info("Update commands: {$this->option('updatecmd')}");
        $this->info("Delete slash commands: {$this->option('delcmd')}");
        $this->info("========================");

        if(!$this->option('dev') && !$this->confirm('Running bot in production mode, this means loading/updating ALL commands, are you sure?')) {
            $this->info("try adding the --dev flag");
            exit;
        }
        $bot = new Bot(
            $this->option('dev'),
            $this->option('updatecmd'),
            $this->option('delcmd')
        );

        $discord = ($bot->discord());
        $discord->run();

        return Command::SUCCESS;
    }
}
