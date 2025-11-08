<?php

namespace App\Console\Commands;

use App\Services\TyphoonTrackingService;
use Illuminate\Console\Command;

class UpdateTyphoonPositions extends Command
{
    protected $signature = 'typhoons:update';
    protected $description = 'Update typhoon positions and tracking data';

    public function handle(TyphoonTrackingService $typhoonService): int
    {
        $this->info('Updating typhoon positions...');

        $count = $typhoonService->updateTyphoonPositions();

        $this->info("Successfully updated {$count} typhoon(s).");

        return Command::SUCCESS;
    }
}
