<?php

namespace App\Http\Traits;

use App\Models\Tenant;
use App\Models\Country;
use App\Models\RoomType;
use App\Models\BusinessSource;
use App\Models\FolioOperationCharge;
use Illuminate\Support\Facades\Cache;

trait CachedQueries
{
    public static function businessSources()
    {
        $tenant = auth()->user()->current_tenant_id;
        return Cache::rememberForever('business_sources_' . $tenant, function () {
            return BusinessSource::orderBy('name', 'DESC')->get();
        });
    }
    public static function folioOperationCharges()
    {
        $tenant = auth()->user()->current_tenant_id;
        return Cache::rememberForever('folio_operation_charges_' . $tenant, function () {
            return FolioOperationCharge::all();
        });
    }
    public static function roomTypes()
    {
        $tenant = auth()->user()->current_tenant_id;
        return Cache::remember('room_types_' . $tenant, now()->addHours(24), function () {
            return RoomType::whereHas('rooms')->with('ratePlans')->get();
        });
    }

    public static function countries()
    {
        return Cache::rememberForever('countries', function () {
            return Country::all();
        });
    }
}
