<?php

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\RatePlan;
use App\Models\RoomType;
use App\Enums\PaymentType;
use App\Filament\Resources\BookingResource;
use Faker\Factory as Faker;
use App\Models\ChannelGroup;
use App\Models\BusinessSource;
use Filament\Facades\Filament;
use Faker\Provider\en_US\Address;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Jobs\ReservationStatusObserver;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use App\Http\Controllers\Pdf\ReservationInvoice;

Route::get('/', function () {

    return RoomType::with('ratePlans')->latest()->first();
    $selection =  ["1_1", "1_2", "2_1"];

    $roomTypeSlc = [];
    foreach ($selection as $roomTypeSelection) {
        $roomTypeId = explode('_', $roomTypeSelection)[0];
        $roomTypeSlc[][$roomTypeId] = explode('_', $roomTypeSelection)[1];
    }

    return $result = collect($roomTypeSlc)
        ->groupBy(function ($item) {
            return key($item);
        })
        ->map(function ($items) {
            return RoomType::with(['ratePlans' => function ($q) use ($items) {
                return $q->wherePivotIn('rate_plan_id', $items->pluck(key($items[0])));
            }])->find(key($items[0]));
            return $items
                ->pluck(key($items[0]))
                ->flatten()
                ->unique()
                ->values();
        })
        ->flatten();

    return redirect('/admin');
});
Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('/pdf/reservation-invoice/{booking_id}', ReservationInvoice::class)->name('pdf.reservation-invoice');
});
