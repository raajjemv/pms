<x-filament-panels::page wire:poll.60s>
    <div x-init="() => {
        var date = '{{ !request('date') ? now()->format('d') : '' }}';
        if (date && date > '09') {
            var container = document.getElementById('scheduler-wrapper');
            const targetDiv = document.getElementById('day-' + date);
            const targetOffset = targetDiv.offsetLeft;
            container.scrollTo({ left: targetOffset, behavior: 'smooth' });
    
        }
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

    <div x-data="{
        roomId: '',
        selectedDays: [],
        startGridDate: null,
        endGridDate: null,
        isSelecting: false,
    
        selectDays() {
            const start = parseInt(this.startGridDate);
            const end = parseInt(this.endGridDate);
            this.selectedDays = [];
    
            for (let i = start; i <= end; i++) {
                this.selectedDays.push(i);
            }
        },
        isDateWithinRange(dateToCheck, room) {
            const checkDate = new Date(dateToCheck);
            const start = new Date(this.startGridDate);
            const end = new Date(this.endGridDate);
    
            if (isNaN(checkDate) || isNaN(start) || isNaN(end)) {
                return false;
            }
    
            return checkDate >= start && checkDate <= end && room == this.roomId;
        }
    }">
        <button @click="isDateWithinRange('2024-09-05',2) ? console.log('ok') : null">ss</button>

        <div class="absolute top-0 left-0 z-10 p-2 text-white bg-black rounded-lg">
            <p x-text="startGridDate"></p>
            <p x-text="endGridDate"></p>
        </div>
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
                                            @livewire('pms.room-rate', ['roomNumbers' => $roomNumbers, 'day' => $day], key(str()->random()))
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
                                        <div wire:key="selection-day-{{ $day }}" day="{{ $day }}"
                                            :class="{
                                                'bg-gray-500': isDateWithinRange('{{ $day }}',
                                                    {{ $room->id }})
                                            }"
                                            @mousedown="isSelecting = true; startGridDate = $event.target.getAttribute('day');roomId = '{{ $room->id }}'"
                                            @mouseup="isSelecting = false; endGridDate = $event.target.getAttribute('day'); selectDays($event)"
                                            @mousemove="isSelecting && (endGridDate = $event.target.getAttribute('day'))"
                                            class="flex-none  flex items-center w-[90px] px-1  border-[0.8px] border-gray-200">
                                        </div>
                                    @endforeach

                                    @foreach ($room->bookingReservations as $reservation)
                                        @php
                                            $from = $reservation->from;
                                            $to = $reservation->to;
                                            $totalDays = $from->diffInDays($to);
                                            $dayNumber = $reservation->from->day;
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
                                            wire:click="viewBookingSummary('{{ $reservation->booking_id }}','{{ $reservation->id }}')"
                                            class="absolute bg-green-500 h-full flex items-center overflow-hidden rounded  border-[0.8px] border-gray-200">
                                            <div class="px-1 text-white rounded whitespace-nowrap">
                                                {{ $reservation->customer->name }} </div>
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

    <x-filament::modal closeEventName="close-reservation-modal" id="booking-summary" slide-over :width="$bookingSummary?->bookingReservations->count() > 1 ? 'xl' : 'sm'">
        {{-- <x-booking-scheduler.booking-summary :booking="$bookingSummary" /> --}}
        @if ($bookingSummary)
            <livewire:pms.reservation.booking-summary :booking="$bookingSummary" :reservation-id="$bookingSummaryReservationId" />
            {{-- <x-pms.reservation.booking-summary :booking="$bookingSummary" /> --}}
        @endif
    </x-filament::modal>
</x-filament-panels::page>
