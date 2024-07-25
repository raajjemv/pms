<?php

use App\Models\User;
use Filament\Facades\Filament;
use Faker\Provider\en_US\Address;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TenantsPermission;
use Spatie\Permission\PermissionRegistrar;
use Faker\Factory as Faker;

Route::get('/', function () {

    return view('welcome');
})
    ->middleware(TenantsPermission::class);
