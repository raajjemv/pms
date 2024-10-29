<x-filament-panels::page wire:poll.60s>
    <div x-init="() => {
        setTimeout(() => {
            var date = '{{ $date }}';
            var container = document.getElementById('scheduler-wrapper');
            if (isCurrentMonthAndYear(date)) {
                var today = new Date().getDate();
                {{-- today = String(today - 2).padStart(2, '0'); --}}
                const targetDiv = document.getElementById('day-' + today);
                const targetOffset = targetDiv.offsetLeft - 200;
                container.scrollTo({ left: targetOffset, behavior: 'smooth' });
            } else {
                container.scrollTo({ left: 0, behavior: 'smooth' });
            }
        }, 0)
    
    }" x-data="{
        isCurrentMonthAndYear(dateString) {
                const currentDate = new Date();
                const currentYear = currentDate.getFullYear();
                const currentMonth = currentDate.getMonth() + 1;
    
                const [year,
                    month
                ] = dateString.split('-');
    
                return year == currentYear && month == currentMonth;
            },
            scrollScheduler(type) {
    
                var container = document.getElementById('scheduler-wrapper');
                if (type == 'right') {
                    container.scrollTo({ left: container.scrollLeft + 200, behavior: 'smooth' });
                } else {
                    container.scrollTo({ left: container.scrollLeft - 200, behavior: 'smooth' });
                }
            }
    }">
        <div class="flex items-center space-x-2">
            <x-form-inputs.flat-pickr-month id="selected_month" wire:model.live="date" />
            <x-filament::button @click="scrollScheduler('left')" icon="heroicon-m-chevron-left" />
            <x-filament::button @click="scrollScheduler('right')" icon="heroicon-m-chevron-right" />
        </div>

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
    
                $wire.mountAction('quickReservationActions', {
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

        <div class="py-2 font-medium">
            <div wire:loading wire:target="date"
                class="font-medium text-center transition-all duration-500 ease-in-out delay-500">Loading Data,
                Please
                wait <i class="fas fa-spin fa-circle-notch"></i>
            </div>

            <div wire:loading.remove wire:target="date" class="flex items-center justify-between">
                <div></div>
                <div>
                    {{ $this->startOfMonth->format('F, Y') }}
                </div>
                <x-booking-scheduler.legends />
            </div>
        </div>
        <div wire:loading.remove wire:target="date" id="scheduler-wrapper"
            class="pb-5 overflow-x-scroll text-sm text-black rounded-lg bg-gray-50">
            <div class="">
                <div class="w-max">
                    <div class="relative flex">
                        <div
                            class="sticky left-0 bg-white w-[200px] flex items-center font-semibold flex-none  px-1 border-[0.8px] border-gray-200 z-20">
                            Rooms
                        </div>
                        @foreach ($this->monthDays as $day)
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
                    @foreach ($this->rooms->groupBy('roomType.name') as $groupKey => $roomNumbers)
                        <div class=" w-max">
                            <div class="relative flex bg-zinc-100">
                                <div
                                    class="bg-zinc-100 sticky left-0 flex-none w-[200px] px-2 flex items-center border-[0.8px] border-gray-300 font-semibold z-20">
                                    {{ $groupKey }}
                                </div>
                                <div class="flex">
                                    @foreach ($this->monthDays as $day)
                                        <div class="flex-none  w-[90px] border-[0.8px] border-gray-300">
                                            <div>
                                                <x-rooms-available-count :day="$day" :roomNumbers="$roomNumbers" />
                                                @livewire('pms.room-rate', ['roomType' => $roomNumbers->first()->roomType, 'day' => $day], key('rates' . $day . '-' . $roomNumbers->first()->room_type_id))
                                               
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
                                        @foreach ($this->monthDays as $day)
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
                                                        : ($from->month == $this->startOfMonth->month
                                                            ? $dayNumber * 90 - 45
                                                            : 0);
                                                $width =
                                                    $from->month == $to->month
                                                        ? $totalDays * 90
                                                        : ($from->month == $this->startOfMonth->month
                                                            ? $totalDays * 90
                                                            : $to->day * 90 - 45);
                                                $exceeding = 0;
                                                if ($to->gt($this->startOfMonth->endOfMonth())) {
                                                    $exceeding = $this->endOfMonth->diffInDays($to) * 90;
                                                    // $exceeding =
                                                    //     round($to->diffInDays($this->startOfMonth->endOfMonth()->format('Y-m-d')));
                                                    $width = $width - $exceeding + 7;
                                                }
                                            @endphp
                                            <div style="width: {{ $width }}px;left:{{ $left }}px"
                                                wire:click="$dispatch('booking-summary',{booking_id:{{ $reservation->booking_id }},reservation_id:{{ $reservation->id }}})"
                                                class="absolute h-full overflow-hidde border-gray-200 cursor-pointer py-0.5">
                                                <div
                                                    class=" w-full h-full flex items-center rounded relative {{ $reservation->status->getColor() }}">
                                                    <div
                                                        class="flex items-end px-1 space-x-1 overflow-hidden text-sm text-white whitespace-nowrap">
                                                        <x-booking-scheduler.icon :booking-type="$reservation->booking->booking_type" />
                                                        <span title="{{ $reservation->customer->name }}"
                                                            class="">
                                                            {{ $reservation->customer->name }}
                                                        </span>
                                                    </div>
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
    </div>

    <livewire:pms.reservation.booking-summary />

    <livewire:pms.reservation.reservation />

    <x-filament::modal :close-by-clicking-away="false" id="new-booking" width="7xl" :autofocus="false">
        <x-slot name="heading">
            <div>New Booking</div>


        </x-slot>
        <livewire:pms.reservation.new-booking />
    </x-filament::modal>



</x-filament-panels::page>
