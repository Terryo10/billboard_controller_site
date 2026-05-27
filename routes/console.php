<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule as ScheduleFacade;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled tasks
ScheduleFacade::command('stations:check-offline')->everyFiveMinutes();
ScheduleFacade::command('invoices:generate')->dailyAt('06:00');
ScheduleFacade::command('media:cleanup')->dailyAt('02:00');
