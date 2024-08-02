<x-filament-panels::page>
    <div class="overflow-x-scroll text-sm text-black rounded-lg bg-gray-50">
        <div class="flex">
            <div class="w-[200px] flex items-center font-semibold flex-none  px-1 border-[0.8px] border-gray-200">Rooms
            </div>
            @foreach ($monthDays as $day)
                <div
                    class="flex-none border-[0.8px] border-gray-200 flex items-center justify-center w-[90px] px-1 py-1">
                    <div class="text-center">
                        <div>{{ $day->format('D') }}</div>
                        <div class="font-semibold">{{ $day->format('d') }}</div>
                        <div>{{ $day->format('M') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="">
            @foreach ($rooms->groupBy('roomType.name') as $groupKey => $roomNumbers)
                <div class="overflow-hidden w-max">
                    <div class="flex bg-zinc-200">
                        <div
                            class="flex-none w-[200px] px-2 flex items-center border-[0.8px] border-gray-300 font-semibold">
                            {{ $groupKey }}
                        </div>
                        <div class="flex">
                            @foreach ($monthDays as $day)
                                <div
                                    class="flex-none  flex items-center w-[90px] px-1 py-5 border-[0.8px] border-gray-300">

                                </div>
                            @endforeach
                        </div>
                    </div>
                    @foreach ($roomNumbers as $room)
                        <div class="flex ">
                            <div
                                class="flex-none w-[200px] flex items-center px-1 border-[0.8px] border-gray-200 font-medium pl-3 py-1">
                                {{ $room->room_number }}</div>
                            <div class="relative flex">
                                @foreach ($monthDays as $day)
                                    <div
                                        class="flex-none  flex items-center w-[90px] px-1  border-[0.8px] border-gray-200">

                                    </div>
                                @endforeach
                                @php
                                    $bookings = $room->bookings;
                                @endphp
                                @foreach ($bookings as $booking)
                                    @php
                                        $from = $booking->from;
                                        $to = $booking->to;
                                        $totalDays = $from->diffInDays($to);
                                        $dayNumber = $booking->from->day;
                                        $left =
                                            $from->month == $to->month
                                                ? $dayNumber * 90 - 45
                                                : ($from->month == $startOfMonth->month
                                                    ? $dayNumber * 90 - 45
                                                    : 0);

                                        $width =
                                            $from->month == $to->month
                                                ? $totalDays * 90
                                                : ($from->month == $startOfMonth->month
                                                    ? $totalDays * 90
                                                    : $to->day * 90 - 45);
                                    @endphp
                                    <div style="width: {{ $width }}px;left:{{ $left }}px"
                                        class="absolute bg-green-500 h-full flex items-center overflow-hidden rounded  border-[0.8px] border-gray-200">
                                        <div class="px-1 text-white rounded ">{{ $booking->customer->name }} </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
