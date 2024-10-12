<?php

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\RatePlan;
use App\Models\RoomType;
use App\Enums\PaymentType;
use App\Http\Controllers\Pdf\ReservationInvoice;
use App\Models\BookingReservation;
use Faker\Factory as Faker;
use App\Models\ChannelGroup;
use App\Models\BusinessSource;
use Filament\Facades\Filament;
use Faker\Provider\en_US\Address;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;

Route::get('/', function () {

    $startOfMonth = Carbon::parse('2024-10-14');
    $endOfMonth = Carbon::parse('2024-10-15');
    return BookingReservation::query()
        ->whereHas('room', function ($q) {
            $q->where('room_type_id', 2);
        })
        ->whereBetween('from', [$startOfMonth, $endOfMonth])
        ->get();
    $room = Room::query()
        ->withCount(['bookingReservations' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('from', [$startOfMonth, $endOfMonth])
                ->orWhereBetween('to', [$startOfMonth, $endOfMonth]);
        }])
        ->find(6);
    return $room;
    return view('welcome');
});
Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('/pdf/reservation-invoice/{booking_id}', ReservationInvoice::class)->name('pdf.reservation-invoice');
});
