<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\ReservationStatusObserver;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new ReservationStatusObserver)
    ->everyMinute()
    ->between('11:00', '23:30');
