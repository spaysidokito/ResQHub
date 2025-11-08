<?php

namespace App\Console\Commands;

use App\Services\DisasterService;
use Illuminate\Console\Command;

class FetchDisasters extends Command
{
    protected $signature = 'disasters:fetch';
    protected $description = 'Fetch latest disaster data from external APIs';

    public function handle(DisasterService $disasterService): int
    {
        $this->info('Fetching disaster data...');

        $count = $disasterService->fetchAndStoreDisasters();

        $this->info("Successfully fetched and stored {$count} disasters.");

        return Command::SUCCESS;
    }
}
