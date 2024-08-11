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
    $startOfMonth = request('date') ? Carbon::parse(request('date'))->startOfMonth() : Carbon::parse('2024-08-01');

    $endOfMonth = request('date') ?  Carbon::parse(request('date'))->endOfMonth() : Carbon::parse('2024-08-30');

    $types = RoomType::whereHas('ratePlans')->with([
        'ratePlans',
        'rates' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('date', '>=', $startOfMonth)
                    ->where('date', '<=', $endOfMonth);
            })->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('date', '>=', $startOfMonth)
                    ->where('date', '<=', $endOfMonth);
            })->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('date', '<', $startOfMonth)
                    ->where('date', '>', $endOfMonth);
            });
        }
    ])->get();

    return $types;
    return view('welcome');
});
