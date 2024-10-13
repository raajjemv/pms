@props(['day', 'roomNumbers'])
<div class="py-3 text-center">
    <div class="inline-block px-2 py-0.5 rounded  bg-red-200 text-xs">
        @php
            $ss = $roomNumbers
                ->pluck('bookingReservations')
                ->flatten()
                ->unique('id')
                ->filter(function ($b) use ($day) {
                    return $b['from'] <= $day->setTime(14, 0, 0) && $b['to'] > $day->setTime(12, 0, 0);
                })

                ->count();
            echo $roomNumbers->count() - $ss;

        @endphp
    </div>

</div>
