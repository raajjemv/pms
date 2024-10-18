<?php

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\RatePlan;
use App\Models\RoomType;
use App\Enums\PaymentType;
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
use Illuminate\Support\Facades\Route;
use App\Jobs\ReservationStatusObserver;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use App\Http\Controllers\Pdf\ReservationInvoice;

Route::get('/', function () {
    $today = now();

    $reservations = BookingReservation::query()
        ->with('tenant')
        ->withoutGlobalScopes()
        ->whereDate('to', '<=', $today)
        ->where('status',  'check-in')
        ->take(100)
        ->get();

    return $filter = $reservations->filter(function ($reservation) use (&$today) {
        $tenant_check_out_time = Carbon::createFromFormat('H:i', $reservation->tenant->check_out_time);
        return $today->gte($tenant_check_out_time);
        return $today->gte($tenant_check_out_time) && $reservation->to->gte($tenant_check_out_time);
    });

    return redirect('/admin');
});
Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('/pdf/reservation-invoice/{booking_id}', ReservationInvoice::class)->name('pdf.reservation-invoice');
});
