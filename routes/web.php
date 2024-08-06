<?php

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use Faker\Factory as Faker;
use Filament\Facades\Filament;
use Faker\Provider\en_US\Address;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TenantsPermission;
use App\Models\Booking;
use App\Models\RoomType;
use Spatie\Permission\PermissionRegistrar;

Route::get('/', function () {

    $booking = Booking::first();
    $bookingNights = $booking->bookingNights;
    return $booking->bookingNights->avg('rate');

    return view('welcome');
});
