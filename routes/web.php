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
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use function Spatie\LaravelPdf\Support\pdf;

Route::get('/', function () {
    return pdf()
        ->view('pdf.reservation-invoice')
        ->withBrowsershot(function (Browsershot $browsershot) {
            $browsershot->setChromePath(env('CHROME_PATH'));
        })
        ->disk('s3')
        ->save('invoice-2023-04-10.pdf');
    return view('welcome');
});
