<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule real-time disaster data fetching
Schedule::command('earthquakes:fetch')->everyFiveMinutes();
Schedule::command('disasters:clear-test')->daily();
