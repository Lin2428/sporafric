<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

 Schedule::command('app:synchronization-automatic')->everySixHours();
Schedule::command('app:synchronization-automatic')->dailyAt('06:00');
// Schedule::command('app:synchronization-automatic')->everyTwoMinutes();
