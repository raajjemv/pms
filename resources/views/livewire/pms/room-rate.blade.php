<div class="text-center my-0.5" >
    @php
        $rate = roomTypeRate($roomTypeId, $day->format('Y-m-d'));
    @endphp

    <span class="text-xs">{{ Number::currency($rate ?? 0) }}</span>
</div>
