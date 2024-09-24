@props(['day', 'roomNumbers'])
<div class="py-3 text-center">
    <div class="inline-block px-2 py-0.5 rounded  bg-red-200 text-xs">
        @php
            $ss = $roomNumbers
                ->pluck('bookingReservations')
                ->flatten()
                ->unique('booking_id')
                ->filter(function ($b) use ($day) {
                    return $b['from'] <= $day && $b['to'] > $day;
                })

                ->count();
            echo $roomNumbers->count() - $ss;

        @endphp
    </div>

</div>
