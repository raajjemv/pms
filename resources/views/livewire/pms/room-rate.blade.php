<div class="text-center my-0.5">
    @php
        // $roomTypeRateByDay =
        //     $roomNumbers
        //         ->first()
        //         ->roomType->rates->where('date', $day->format('Y-m-d'))
        //         ->where('rate_plan_id', defaultRatePlan()->id)
        //         ->first()->rate ?? $roomNumbers->first()->roomType->base_rate;

        $rate = roomTypeBaseRate($roomNumbers->first()->room_type_id, $day->format('Y-m-d'));
    @endphp

    <span class="text-xs">{{ Number::currency($rate) }}</span>
</div>
