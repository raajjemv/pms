<?php

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use App\Models\RatePlan;
use App\Models\RoomType;
use Faker\Factory as Faker;
use Filament\Facades\Filament;
use Faker\Provider\en_US\Address;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TenantsPermission;
use Spatie\Permission\PermissionRegistrar;

Route::get('/', function () {
    $fridays = [];
    $startDate = Carbon::now()->startofmonth()->modify('this sunday'); // Get the first friday. If $fromDate is a friday, it will include $fromDate as a friday
    $endDate = Carbon::now()->endOfMonth();

    for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {
        $fridays[] = $date->format('Y-m-d');
    }
    return $fridays;
    return view('welcome');
});
