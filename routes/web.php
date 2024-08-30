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
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\TenantsPermission;
use App\Models\ChannelGroup;
use Spatie\Permission\PermissionRegistrar;

Route::get('/', function () {
    
    return view('welcome');
});
