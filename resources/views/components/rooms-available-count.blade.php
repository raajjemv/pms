@props(['day', 'roomNumbers'])
<div class="py-3 text-center">
    <div class="inline-block px-2 py-0.5 rounded  bg-red-200 text-xs">
        @php
            $ss = $roomNumbers
                ->pluck('bookingReservations')
                ->flatten()
                ->unique('id')
                ->filter(function ($b) use ($day) {
                    return $b['from'] <= $day->setTimeFromTimeString(tenant()->check_out_time) && $b['to'] > $day->setTimeFromTimeString(tenant()->check_in_time);
                })

                ->count();
            echo $roomNumbers->count() - $ss;

        @endphp
    </div>

</div>
