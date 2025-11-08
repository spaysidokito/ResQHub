<?php

namespace App\Console\Commands;

use App\Services\EarthquakeService;
use Illuminate\Console\Command;

class FetchEarthquakes extends Command
{
    protected $signature = 'earthquakes:fetch {--days=7} {--min-magnitude=2.5}';
    protected $description = 'Fetch latest earthquake data from USGS API';

    public function handle(EarthquakeService $service): int
    {
        $this->info('Fetching earthquake data from USGS...');

        $days = (int) $this->option('days');
        $minMagnitude = (float) $this->option('min-magnitude');

        $earthquakes = $service->fetchRecentEarthquakes($days, $minMagnitude);

        if (empty($earthquakes)) {
            $this->warn('No new earthquake data found.');
            return Command::SUCCESS;
        }

        $this->info('Processing earthquakes and creating alerts...');

        foreach ($earthquakes as $earthquake) {
            $service->checkAndCreateAlerts($earthquake);
        }

        $this->info("Successfully fetched " . count($earthquakes) . " earthquakes.");

        return Command::SUCCESS;
    }
}
