<?php

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use Faker\Factory as Faker;
use Filament\Facades\Filament;
use Faker\Provider\en_US\Address;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TenantsPermission;
use App\Models\RoomType;
use Spatie\Permission\PermissionRegistrar;

Route::get('/', function () {

    $startOfMonth = Carbon::parse('2024-08-01');

    $endOfMonth = Carbon::parse('2024-08-30');

    $roomTypes = RoomType::with('rooms.bookings')->find(4);

    return $roomTypes->rooms->pluck('bookings')->flatten()->unique('room_id');

    return view('welcome');
});
