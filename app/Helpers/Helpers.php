<?php

use App\Models\ChannelGroup;
use Carbon\Carbon;
use App\Models\RatePlan;
use App\Models\RoomType;
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
if (! function_exists('defaultChannelGroup')) {
    function defaultChannelGroup()
    {
        if (Auth::check()) {
            $tenant_id = auth()->user()->current_tenant_id;
            return Cache::rememberForever('default_channel_group_' . $tenant_id, function () {
                return ChannelGroup::where('default', true)->first();
            });
        }
    }
}
if (! function_exists('tenant')) {
    function tenant()
    {
        if (Auth::check()) {
            return auth()->user()->tenant;
        }
    }
}
if (! function_exists('roomTypeBaseRate')) {
    function roomTypeBaseRate($roomTypeId, $from)
    {
        if (Auth::check()) {
            $roomType = RoomType::find($roomTypeId);
            return $roomType->rates->where('date', $from)
                ->where('rate_plan_id', defaultRatePlan()->id)
                ->first()
                ->rate ?? $roomType->base_rate;
        }
    }
}
if (! function_exists('totolNights')) {
    function totolNights($from, $to)
    {
        if (Auth::check()) {
            $total = Carbon::parse($from)->diffInDays(Carbon::parse($to));

            return round($total);
        }
    }
}
