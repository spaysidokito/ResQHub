<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule real-time disaster data fetching
Schedule::command('earthquakes:fetch')->everyFiveMinutes();
Schedule::command('disasters:fetch')->everyTenMinutes();
Schedule::command('pagasa:scrape')->everyFifteenMinutes();
Schedule::command('typhoons:update')->everyTenMinutes();
Schedule::command('alerts:generate')->hourly();
Schedule::command('disasters:clear-test')->daily();
