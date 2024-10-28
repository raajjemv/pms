<div class="text-center my-0.5">
    {{-- @php
        $rate = roomTypeRate($roomTypeId, $day->format('Y-m-d'));
    @endphp

    <span class="text-xs">{{ Number::currency($rate ?? 0) }}</span> --}}
    @php
        $inventoryRate = $roomType->rates->where('date', $day->format('Y-m-d'))->first()?->rate;
        $defaultRate = $roomType->ratePlans->where('pivot.default', true)->first()?->pivot->rate;
        $rate = $inventoryRate ? $inventoryRate : $defaultRate;
    @endphp
    <span class="text-xs">{{ Number::currency($rate ?? 0) }}</span>
</div>
