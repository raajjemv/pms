<?php

use App\Models\RatePlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

if (! function_exists('defaultRatePlan')) {
    function defaultRatePlan()
    {
        if (Auth::check()) {
            $tenant_id = auth()->user()->current_tenant_id;
            return Cache::rememberForever('default_rate_plan_' . $tenant_id, function () {
                return RatePlan::where('default', true)->first();
            });
        }
    }
}
