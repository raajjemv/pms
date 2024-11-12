@props(['day', 'roomNumbers', 'unassignedRooms' => null])
<div class="py-3 text-center">
    <div class="inline-block px-2 py-0.5 rounded  bg-red-200 text-xs" title="Vacant Rooms">
        @php
            $available = $roomNumbers
                ->pluck('bookingReservations')
                ->flatten()
                ->unique('id')
                ->filter(function ($b) use ($day) {
                    return $b->from->lte($day->setTimeFromTimeString(tenant()->check_in_time)) &&
                        $b->to->gt($day->setTimeFromTimeString(tenant()->check_out_time));
                //  
                //     return $b->from <= $day->setTimeFromTimeString(tenant()->check_in_time) &&
                //         $b['to'] > $day->setTimeFromTimeString(tenant()->check_out_time);
                })

                ->count();
            echo $roomNumbers->count() - $available;

        @endphp
    </div>
    @if ($unassignedRooms->count())
        <button @click="$dispatch('bulk-room-assign',{ ids: {{ $unassignedRooms->pluck('id') }}})"
            class="inline-block px-2 py-0.5 rounded  bg-blue-200 text-xs" title="Unassigned Rooms">
            {{ $unassignedRooms->count() }}
        </button>
    @endif

</div>
