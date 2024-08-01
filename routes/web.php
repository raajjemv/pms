<?php

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use Faker\Factory as Faker;
use Filament\Facades\Filament;
use Faker\Provider\en_US\Address;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TenantsPermission;
use Spatie\Permission\PermissionRegistrar;

Route::get('/', function () {


    $room = Room::with('roomType', 'bookings')->first();
    return $bookings = $room
        ->bookings()
        ->whereDate('from', '<=', '2024-07-03')
        ->whereDate('to', '>=', '2024-07-06')
        ->get();
    return $rooms->groupBy('roomType.name');
    return view('welcome');
});
