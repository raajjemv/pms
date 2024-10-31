<?php

use Carbon\Carbon;
use App\Models\Room;
use App\Models\RatePlan;
use App\Models\RoomType;
use App\Enums\PaymentType;
use App\Models\ChannelGroup;
use Filament\Facades\Filament;
use App\Models\BookingReservation;
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
                ?->pivot
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
                ?->pivot
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
            $total = Carbon::parse($from)->diffInDays(Carbon::parse($to)->setTime(0, 0, 0));

            return round($total);
        }
    }
}


//total nights by date
if (! function_exists('totolNightsByDates')) {
    function totolNightsByDates($from, $to)
    {
        if (Auth::check()) {
            $dates = [];
            $totalNightsLeft = totolNights($from, $to);
            for ($i = 0; $i < $totalNightsLeft; $i++) {
                $future_date = now()->copy()->addDays($i);
                $dates[] = $future_date;
            }
            return collect($dates);
        }
    }
}


//total nights for the booking period
if (! function_exists('reservationTotals')) {
    function reservationTotals($reservationId)
    {
        if (Auth::check() && $reservationId) {
            return Cache::remember('reservationBalance_' . $reservationId, now()->addHour(), function () use ($reservationId) {
                $reservation = BookingReservation::withTrashed()->find($reservationId);
                $total = $reservation
                    ->bookingTransactions
                    ->whereNotIn('transaction_type', PaymentType::getAllValues())
                    ->sum('rate');
                $paid = $reservation->bookingTransactions
                    ->whereIn('transaction_type', PaymentType::getAllValues())
                    ->sum('rate');
                return [
                    'total' => $total,
                    'paid' => $paid,
                    'balance' => $total - $paid
                ];
            });
        }
        return 0;
    }
}
//total nights for the booking period
if (! function_exists('roomReservationsByMonth')) {
    function roomReservationsByMonth($roomId, $from, $to)
    {
        $startOfMonth = Carbon::parse($from)->format('Y-m-d');
        $endOfMonth = Carbon::parse($to)->format('Y-m-d');

        $roomReservationDates =  Room::with(['bookingTransactions' => function ($q) use ($startOfMonth, $endOfMonth) {
            return $q
                ->where('transaction_type', 'room_charge')
                // ->where('maintenance', false)
                ->whereBetween('date', [$startOfMonth, $endOfMonth]);
        }])
            ->find($roomId)
            ->bookingTransactions
            ->pluck('date');

        return collect($roomReservationDates)->map(fn($date) => Carbon::parse($date)->format('Y-m-d'));
    }
}

//clear scheduler cache
if (! function_exists('clearSchedulerCache')) {
    function clearSchedulerCache($from, $to)
    {
        $startOfMonth = Carbon::parse($from)->format('Y-m');
        $endOfMonth = Carbon::parse($to)->format('Y-m');

        Cache::forget('scheduler_' . $startOfMonth);
        Cache::forget('scheduler_' . $endOfMonth);
    }
}
