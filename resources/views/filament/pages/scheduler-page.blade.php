<x-filament-panels::page>
    <div x-init="() => {
        var container = document.getElementById('scheduler-wrapper');
        const targetDiv = document.getElementById('day-20');
        const targetOffset = targetDiv.offsetLeft;
        container.scrollTo({ left: targetOffset, behavior: 'smooth' });
    }" x-data="{
        scrollScheduler(type) {
            var container = document.getElementById('scheduler-wrapper');
            if (type == 'right') {
                container.scrollTo({ left: container.scrollLeft + 200, behavior: 'smooth' });
            } else {
                container.scrollTo({ left: container.scrollLeft - 200, behavior: 'smooth' });
            }
        }
    }">
        <x-filament::button @click="scrollScheduler('left')" icon="heroicon-m-chevron-left" />

        <x-filament::button @click="scrollScheduler('right')" icon="heroicon-m-chevron-right" />
    </div>
    <div>
        <div class="py-2 font-medium">
            <div class="text-center">{{ $startOfMonth->format('F, Y') }}</div>
        </div>
        <div id="scheduler-wrapper" class="overflow-x-scroll text-sm text-black rounded-lg bg-gray-50">
            <div class="w-max">
                <div class="relative flex">
                    <div
                        class="sticky left-0 bg-white w-[200px] flex items-center font-semibold flex-none  px-1 border-[0.8px] border-gray-200">
                        Rooms
                    </div>
                    @foreach ($monthDays as $day)
                        <div id="day-{{ $day->format('d') }}" @class([
                            'flex-none border-[0.8px] border-gray-200 flex items-center justify-center w-[90px] px-1 py-1',
                            'bg-amber-100' => $day->isFriday() || $day->isSaturday(),
                        ])>
                            <div class="text-center">
                                <div>{{ $day->format('D') }}</div>
                                <div class="font-semibold">{{ $day->format('d') }}</div>
                                <div>{{ $day->format('M') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="">
                @foreach ($rooms->groupBy('roomType.name') as $groupKey => $roomNumbers)
                    <div class=" w-max">
                        <div class="relative flex bg-zinc-200">
                            <div
                                class="bg-zinc-200 sticky left-0 flex-none w-[200px] px-2 flex items-center border-[0.8px] border-gray-300 font-semibold">
                                {{ $groupKey }}
                            </div>
                            <div class="flex">
                                @foreach ($monthDays as $day)
                                    <div class="flex-none  w-[90px] border-[0.8px] border-gray-300">
                                        <div>
                                            <x-rooms-available-count :day="$day" :roomNumbers="$roomNumbers" />
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @foreach ($roomNumbers as $room)
                            <div class="relative flex ">
                                <div
                                    class="sticky left-0 z-10 bg-white flex-none w-[200px] flex items-center px-1 border-[0.8px] border-gray-200 font-medium pl-3 py-1">
                                    {{ $room->room_number }}</div>
                                <div class="relative flex overflow-hidden">
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
                                            wire:click="viewBookingSummary('{{ $booking->id }}')"
                                            class="absolute bg-green-500 h-full flex items-center overflow-hidden rounded  border-[0.8px] border-gray-200">
                                            <div class="px-1 text-white rounded whitespace-nowrap">
                                                {{ $booking->customer->name }} </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <x-filament::modal id="booking-summary" slide-over>
        <x-booking-scheduler.booking-summary :booking="$bookingSummary" />
    </x-filament::modal>
</x-filament-panels::page>
