<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disasters:clear-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all test/simulated disasters and keep only real-time data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing test data...');

        // Delete simulations
        $simulations = \App\Models\Disaster::where('source', 'SIMULATION')->count();
        \App\Models\Disaster::where('source', 'SIMULATION')->delete();
        $this->info("Deleted {$simulations} simulation(s)");

        // Delete test alerts
        $testAlerts = \App\Models\Disaster::where('source', 'TEST_ALERT')->count();
        \App\Models\Disaster::where('source', 'TEST_ALERT')->delete();
        $this->info("Deleted {$testAlerts} test alert(s)");

        // Delete admin-created disasters
        $adminCreated = \App\Models\Disaster::where('source', 'Admin')->count();
        \App\Models\Disaster::where('source', 'Admin')->delete();
        $this->info("Deleted {$adminCreated} admin-created disaster(s)");

        // Show remaining disasters
        $remaining = \App\Models\Disaster::count();
        $this->info("Remaining disasters: {$remaining}");

        if ($remaining > 0) {
            $this->info("\nRemaining disaster sources:");
            $sources = \App\Models\Disaster::select('source')
                ->selectRaw('count(*) as count')
                ->groupBy('source')
                ->get();

            foreach ($sources as $source) {
                $this->line("  - {$source->source}: {$source->count}");
            }
        }

        $this->info("\n✅ Test data cleared successfully!");

        return 0;
    }
}
