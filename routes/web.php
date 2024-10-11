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
    $room = Room::with('roomType')->find(1);
    return $room->RoomType;
    return view('welcome');
});
Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('/pdf/reservation-invoice/{booking_id}', ReservationInvoice::class)->name('pdf.reservation-invoice');
});
