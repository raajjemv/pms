<?php

namespace App\Http\Traits;

use App\Models\BusinessSource;
use App\Models\FolioOperationCharge;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

trait CachedQueries
{
    public static function businessSources()
    {
        $tenant = auth()->user()->current_tenant_id;
        return Cache::rememberForever('business_sources_' . $tenant, function () {
            return BusinessSource::all();
        });
    }
    public static function folioOperationCharges()
    {
        $tenant = auth()->user()->current_tenant_id;
        return Cache::rememberForever('folio_operation_charges_' . $tenant, function () {
            return FolioOperationCharge::all();
        });
    }
}
