<?php

namespace App\Console\Commands;

use App\Console\SlashManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Slash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:slash
    {--update : Update and register slash commands}
    {--delete : Delete all slash commands}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register & update slash commands';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $slashManager = new SlashManager();

        if ($this->option('update')) {
            $this->info('Updating Slash Commands.. This may take a while..');

            foreach ($slashManager->register() as $command) {
                $this->info("Registered command: {$command}");
            }

        } elseif ($this->option('delete')) {
            $this->info('Deleting Slash Commands.. This may take a while..');

            foreach ($slashManager->globalCommands() as $command) {
                $slashManager->delete($command['id']);
                $this->info("Deleted command: {$command['id']} - {$command['name']}");
            }
            foreach ($slashManager->guildCommands() as $command) {
                $slashManager->delete($command['id'], true);
                $this->info("Deleted command: {$command['id']} - {$command['name']}");
            }

        } else {
            $this->line("Global Application Commands:");
            $this->printCommands($slashManager->globalCommands());
            $this->line(" ");
            $this->line("Guild Application Commands:");
            $this->printCommands($slashManager->guildCommands());
        }

        return Command::SUCCESS;
    }

    private function printCommands($commands): void
    {
        foreach ($commands as $command) {
            $this->line(" " . $command['name']);
            foreach ($command['options'] as $option) {
                $this->output->writeln("  <info>" . $option['name'] . "</info>");
                if (isset($option['options'])) {
                    foreach ($option['options'] as $subOption) {
                        if ($subOption['type'] === 1) {
                            $this->output->writeln("   <info>" . $subOption['name'] . '</info>');
                        }
                    }
                }
            }
        }
    }
}
