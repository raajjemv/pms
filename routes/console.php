<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\ReservationStatusObserver;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::job(new ReservationStatusObserver)
    ->everyMinute()
    ->between('11:00', '23:30');
