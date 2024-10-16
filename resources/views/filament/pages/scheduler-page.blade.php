<x-filament-panels::page wire:poll.60s>
    <div x-init="() => {
        var date = '{{ !request('date') ? now()->format('d') : '' }}';
        if (date && date >= '09') {
            date = String(date - 2).padStart(2, '0');
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
    
        getDaysBetweenDates(startDateStr, endDateStr) {
            const startDate = new Date(startDateStr);
            const endDate = new Date(endDateStr);
            const millisecondsDifference = endDate - startDate;
    
            const daysDifference = millisecondsDifference / (1000 * 60 * 60 * 24);
    
            return daysDifference;
        },
        selectDays(event) {
            const roomId = event.currentTarget.dataset.room;
            const roomTypeId = event.currentTarget.dataset.type;
            const start = parseInt(this.startGridDate);
            const end = parseInt(this.endGridDate);
            this.selectedDays = [];
    
            for (let i = start; i <= end; i++) {
                this.selectedDays.push(i);
            }
    
            if (this.getDaysBetweenDates(this.startGridDate, this.endGridDate) > 0) {
                $dispatch('open-modal', {
                    id: 'new-booking',
                    from: this.startGridDate,
                    to: this.endGridDate,
                    room_id: roomId,
                    room_type_id: roomTypeId
                })
                this.startGridDate = '';
                this.endGridDate = '';
                this.roomId = '';
                this.selectedDays = [];
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
        {{-- <button @click="isDateWithinRange('2024-10-05',2) ? console.log('ok') : null">ss</button> --}}


        <div class="py-2 font-medium">
            <div class="text-center">{{ $startOfMonth->format('F, Y') }}</div>
        </div>
        <div id="scheduler-wrapper" class="pb-5 overflow-x-scroll text-sm text-black rounded-lg bg-gray-50">
            <div class="w-max">
                <div class="relative flex">
                    <div
                        class="sticky left-0 bg-white w-[200px] flex items-center font-semibold flex-none  px-1 border-[0.8px] border-gray-200 z-20">
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
                        <div class="relative flex bg-zinc-100">
                            <div
                                class="bg-zinc-100 sticky left-0 flex-none w-[200px] px-2 flex items-center border-[0.8px] border-gray-300 font-semibold z-20">
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
                                    class="sticky left-0 z-20 bg-white flex-none w-[200px] flex items-center px-1 border-[0.8px] border-gray-200 font-medium pl-3 py-1">
                                    {{ $room->room_number }}</div>
                                <div class="relative flex overflow-hidde">
                                    @foreach ($monthDays as $day)
                                        <div wire:key="selection-day-{{ $day }}" day="{{ $day }}"
                                            :class="{
                                                'bg-gray-200': isDateWithinRange('{{ $day }}',
                                                    {{ $room->id }})
                                            }"
                                            data-room="{{ $room->id }}" data-type="{{ $room->room_type_id }}"
                                            @mousedown="isSelecting = true; startGridDate = $event.target.getAttribute('day');roomId = '{{ $room->id }}'"
                                            @mouseup="isSelecting = false; endGridDate = $event.target.getAttribute('day'); selectDays($event);"
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
                                            class="absolute h-full overflow-hidde border-gray-200 cursor-pointer p-0.5">
                                            <div
                                                class="flex items-center w-full h-full px-1 text-sm  text-white  rounded whitespace-nowrap relative {{ $reservation->status->getColor() }}">
                                                <x-booking-scheduler.icon :booking-type="$reservation->booking->booking_type" />
                                                <span title="{{ $reservation->customer->name }}" class="pl-1">
                                                    {{ $reservation->customer->name }}</span>
                                                @if (reservationTotals($reservation->id)['balance'])
                                                    <span title="pending payment"
                                                        class="absolute top-0 right-0 z-10 flex items-center justify-center -mt-2 text-xs text-white bg-red-500 rounded-full size-4">$</span>
                                                @endif

                                            </div>
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
        @if ($bookingSummary)
            <livewire:pms.reservation.booking-summary :booking="$bookingSummary" :reservation-id="$bookingSummaryReservationId" />
        @endif
    </x-filament::modal>

    <x-filament::modal :close-by-clicking-away="false" id="new-booking" width="7xl">
        <x-slot name="heading">
            New Booking
        </x-slot>
        <livewire:pms.reservation.new-booking />
    </x-filament::modal>
</x-filament-panels::page>
