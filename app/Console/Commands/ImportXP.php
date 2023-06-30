<?php

namespace App\Console\Commands;

use App\Discord\Core\Bot;
use App\Discord\Core\Models\Guild;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ImportXP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'bot:import-xp {guild} {leaderboard}';
    protected $signature = 'bot:import-xp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import MEE6 XP';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
//        $this->info("Finding guild");
//        $guild = Guild::get($this->argument('guild'));
//
//        if (!$guild) {
//            $this->info('Guild not found, quitting..');
//            exit;
//        }
//
//        // 590941503917129743
//        // https://mee6.xyz/en/leaderboard/590941503917129743
//
//
//        $this->info("Guild found...");
//        $this->info("Discord Guild ID: {$this->argument('guild')}");
//        $this->info("Local Guild ID: {$guild->id}");
//        $this->info("Guild Name: {$guild->name}");
//        $this->info("Using leaderboard: {$this->argument('leaderboard')}");
//
//        if (!$this->confirm('Are you sure you want to import XP from the MEE6 leaderboard to this guild?')) {
//            $this->info("Quitting..");
//        }

        $guild = Guild::get("590941503917129743");

//        $browser = new HttpBrowser(HttpClient::create(['timeout' => 60]));
//        $crawler = $browser->request('GET', $this->argument('leaderboard'));
//
//        var_dump($crawler->outerHtml());

        // $driver = ChromeDriver::start();


        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--no-sandbox',
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $options);


        $driver = RemoteWebDriver::create('http://localhost:4444', $capabilities);
        $this->info("Driver created...");

        $driver->get('https://mee6.xyz/en/leaderboard/590941503917129743');
        $driver->findElement(WebDriverBy::cssSelector("body"));

        $crawler = new Crawler($driver->getPageSource());

       // $test = $crawler->filter("#root");

        var_dump($crawler->html());

        $driver->quit();


        return Command::SUCCESS;
    }
}
