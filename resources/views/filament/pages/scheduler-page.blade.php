<x-filament-panels::page>
    <div class="overflow-hidden text-black bg-gray-100 rounded-lg">
        <div class="p-2 text-center">{{ $selectedMonth->format('M Y') }}</div>
        <div class="w-full text-sm text-center">
            <div class="flex">
                <div class="w-20 p-1 border">Room</div>
                <div class="w-20 p-1 border">Status</div>
                @for ($i = 15; $i < $totalDaysInMonth; $i++)
                    <div class="flex-1 p-1 border ">{{ $i }}</div>
                @endfor
            </div>
            <div class="">
                @foreach ($rooms as $room)
                    @php
                        $bookedDays = $room->id == 1 ? collect([4, 5, 6, 7, 8]) : collect([9, 10, 11, 12, 13,14]);
                    @endphp
                    <div class="flex">
                        <div class="w-20 p-1 border">{{ $room->name }}</div>
                        <div class="w-20 p-1 border">V</div>
                        @for ($i = 1 + $bookedDays->count(); $i < $totalDaysInMonth; $i++)
                            <div @class([
                                'flex-1 p-1 border',
                                'bg-red-100' => $room->id == 1 && in_array($i, $bookedDays->toArray()),
                                'bg-blue-100' => $room->id == 2 && in_array($i, $bookedDays->toArray()),
                            ])>

                            </div>
                        @endfor
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
