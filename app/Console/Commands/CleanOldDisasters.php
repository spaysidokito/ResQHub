<?php

namespace App\Console\Commands;

use App\Models\Disaster;
use Illuminate\Console\Command;

class CleanOldDisasters extends Command
{
    protected $signature = 'disasters:clean {--all : Remove all disasters} {--old : Remove disasters older than 7 days}';
    protected $description = 'Clean up old or test disaster data';

    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->cleanAllDisasters();
        }

        if ($this->option('old')) {
            return $this->cleanOldDisasters();
        }

        $this->info('Cleaning test and outdated disaster data...');

        $testSources = ['test', 'manual', 'simulation'];
        $deleted = Disaster::whereIn('source', $testSources)->delete();

        $this->info("Removed {$deleted} test disaster(s).");

        $oldResolved = Disaster::where('status', 'resolved')
            ->where('updated_at', '<', now()->subDays(7))
            ->delete();

        $this->info("Removed {$oldResolved} old resolved disaster(s).");

        return Command::SUCCESS;
    }

    private function cleanAllDisasters(): int
    {
        if (!$this->confirm('Are you sure you want to delete ALL disasters? This cannot be undone.')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $count = Disaster::count();
        Disaster::truncate();

        $this->info("Removed all {$count} disaster(s) from database.");
        $this->info('Run "php artisan disasters:fetch" to populate with fresh real-time data.');

        return Command::SUCCESS;
    }

    private function cleanOldDisasters(): int
    {
        $deleted = Disaster::where('updated_at', '<', now()->subDays(7))->delete();

        $this->info("Removed {$deleted} disaster(s) older than 7 days.");

        return Command::SUCCESS;
    }
}
