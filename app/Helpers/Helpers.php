<?php

use App\Models\ChannelGroup;
use Carbon\Carbon;
use App\Models\RatePlan;
use App\Models\RoomType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

//default channel group for the tenant ie: common poo', agoda pool
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

//default rate for the room type
if (! function_exists('roomTypeBaseRate')) {
    function roomTypeBaseRate($roomTypeId, $ratePlanId = NULL)
    {
        if (Auth::check()) {
            if ($ratePlanId) {
                return roomTypeBaseRateByRatePlan($roomTypeId, $ratePlanId);
            }
            return roomType($roomTypeId)
                ->ratePlans
                ->where('pivot.default', true)
                ->first()
                ->pivot
                ->rate;
        }
    }
}

//default rate for the room type
if (! function_exists('roomTypeBaseRateByRatePlan')) {
    function roomTypeBaseRateByRatePlan($roomTypeId, $ratePlanId)
    {
        if (Auth::check()) {
            return roomType($roomTypeId)
                ->ratePlans
                ->where('id', $ratePlanId)
                ->first()
                ->pivot
                ->rate;
        }
    }
}

//default plan for the room type
if (! function_exists('roomTypeDefaultPlan')) {
    function roomTypeDefaultPlan($roomTypeId)
    {
        if (Auth::check()) {
            return roomType($roomTypeId)->ratePlans->where('pivot.default', true)->first();
        }
    }
}

//room type with ratePlans
if (! function_exists('roomType')) {
    function roomType($roomTypeId)
    {
        if (Auth::check()) {
            return Cache::remember('room_type_' . $roomTypeId, now()->addHour(), function () use ($roomTypeId) {
                return RoomType::with('ratePlans')->find($roomTypeId);
            });
        }
    }
}

//room rate by date for the room type
if (! function_exists('roomTypeRate')) {
    function roomTypeRate($roomTypeId, $from, $ratePlanId = null)
    {
        if (Auth::check()) {
            $roomType = roomType($roomTypeId);
            return $roomType
                ->rates()
                ->where('date', $from)
                ->where('channel_group_id', defaultChannelGroup()->id)
                ->when($ratePlanId, function ($query) use (&$ratePlanId) {
                    $query->where('rate_plan_id', $ratePlanId);
                })
                ->first()
                ->rate ?? roomTypeBaseRate($roomTypeId, $ratePlanId);
        }
    }
}

//total nights for the booking period
if (! function_exists('totolNights')) {
    function totolNights($from, $to)
    {
        if (Auth::check()) {
            $total = Carbon::parse($from)->diffInDays(Carbon::parse($to));

            return round($total);
        }
    }
}
