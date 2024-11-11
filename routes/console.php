<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\ReservationStatusObserver;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::job(new ReservationStatusObserver)
    ->everyMinute();
