<?php

use App\Enums\PaymentType;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use App\Models\BusinessSource;
use App\Models\RatePlan;
use App\Models\RoomType;
use Faker\Factory as Faker;
use Filament\Facades\Filament;
use Faker\Provider\en_US\Address;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\ChannelGroup;
use Spatie\Permission\PermissionRegistrar;

Route::get('/', function () {
    // return PaymentType::cases();
    return in_array("cash", PaymentType::getAllValues());
    $startOfMonth = request('date') ? Carbon::parse(request('date'))->startOfMonth() : Carbon::now()->startOfMonth();

    $endOfMonth = request('date') ?  Carbon::parse(request('date'))->endOfMonth() : Carbon::now()->endOfMonth();

    $rooms = Room::with(['roomType' => function ($q) use ($startOfMonth, $endOfMonth) {
        $q->with(['rates' => function ($qq) use ($startOfMonth, $endOfMonth) {
            $qq->whereBetween('date', [$startOfMonth, $endOfMonth]);
        }]);
    }, 'bookings' => function ($query) use ($startOfMonth, $endOfMonth) {
        $query->with('customer')->where(function ($query) use ($startOfMonth, $endOfMonth) {
            $query->where('from', '>=', $startOfMonth)
                ->where('from', '<=', $endOfMonth);
        })->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
            $query->where('to', '>=', $startOfMonth)
                ->where('to', '<=', $endOfMonth);
        })->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
            $query->where('from', '<', $startOfMonth)
                ->where('to', '>', $endOfMonth);
        });
    }])->get();

    return $rooms->groupBy('roomType.name');
    return redirect('/admin');
    return view('welcome');
});
