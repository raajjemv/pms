<div class="text-center my-0.5">
    @php
        $roomTypeRateByDay =
            $roomNumbers
                ->first()
                ->roomType->rates->where('date', $day->format('Y-m-d'))
                ->where('rate_plan_id', defaultRatePlan()->id)
                ->first()->rate ?? $roomNumbers->first()->roomType->base_rate;
    @endphp
    <span class="text-xs text-white bg-blue-600 px-1 py-0.5 rounded">{{ Number::currency($roomTypeRateByDay) }}</span>
</div>
