<?php

namespace App\Console\Commands;

use App\Services\PAGASAScraperService;
use Illuminate\Console\Command;

class ScrapePAGASA extends Command
{
    protected $signature = 'pagasa:scrape';
    protected $description = 'Scrape typhoon and weather data from PAGASA website';

    public function handle(PAGASAScraperService $scraperService): int
    {
        $this->info('Scraping PAGASA website for typhoon data...');

        $count = $scraperService->scrapeTyphoonData();

        $this->info("Successfully scraped and stored {$count} weather event(s) from PAGASA.");

        return Command::SUCCESS;
    }
}
