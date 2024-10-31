<div>
    @php
        $reservationTotals = $this->selectedFolio ? reservationTotals($this->selectedFolio?->id) : 0;
    @endphp
    <x-filament::modal :close-by-clicking-away="false" :autofocus="false" closeEventName="close-booking-summary-modal"
        id="booking-summary" slide-over :width="$booking?->bookingReservations->where('status', '!=', App\Enums\Status::Maintenance)->count() > 1 ? 'xl' : 'sm'">
        <x-slot name="heading">
            <div class="flex items-center space-x-3">
                <x-svg-icons.location />
                <div>
                    <div class="font-bold">{{ $this->selectedFolio?->booking_customer }}</div>
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>

                        <div class="text-sm font-thin">Maldives</div>
                    </div>
                </div>
            </div>
        </x-slot>

        <div class="flex">

            <div class="flex-1 pr-3">
                <div class="flex space-x-2">
                    <x-filament::button class="flex-1"
                        @click="$dispatch('open-reservation',{booking_id: '{{ $booking?->id }}', reservation_id:'{{ $this->selectedFolio?->id }}' });$dispatch('close-booking-summary-modal',{id: 'booking-summary'})"
                        {{-- href="{{ App\Filament\Pages\EditReservation::getUrl(['record' => encrypt($booking?->id), 'reservation_id' => $this->selectedFolio?->id]) }}"
                        tag="a" --}}>
                        Open
                    </x-filament::button>
                    <x-filament::button color="gray"
                        href="{{ App\Filament\Pages\EditReservation::getUrl(['record' => encrypt($booking?->id), 'activeTab' => 'print-email']) }}"
                        tag="a">
                        Print / E-Mail
                    </x-filament::button>
                    @if (
                        !in_array($this->selectedFolio?->status, [
                            App\Enums\Status::Archived,
                            App\Enums\Status::Cancelled,
                            App\Enums\Status::Void,
                            App\Enums\Status::Disputed,
                            App\Enums\Status::NoShow,
                        ]))


                        <x-filament::dropdown>
                            <x-slot name="trigger">
                                <x-filament::button color="gray" icon="heroicon-m-ellipsis-vertical"
                                    icon-position="after">
                                    More
                                </x-filament::button>
                            </x-slot>

                            <x-filament::dropdown.list>
                                @if (
                                    ($this->selectedFolio?->from->isToday() || $this->selectedFolio?->from->isPast()) &&
                                        in_array($this->selectedFolio?->status->value, ['reserved', 'inquiry', 'hold', 'confirmed', 'paid']))
                                    <x-filament::dropdown.list.item wire:click="bookingSummaryAction('check-in')"
                                        icon="heroicon-m-check-circle">
                                        Check-In
                                    </x-filament::dropdown.list.item>
                                @endif
                                @if (
                                    ($this->selectedFolio?->to->isToday() || $this->selectedFolio?->to->isPast()) &&
                                        in_array($this->selectedFolio?->status->value, ['check-in', 'overstay']))
                                    <x-filament::dropdown.list.item wire:click="bookingSummaryAction('check-out')"
                                        icon="heroicon-m-arrow-right-start-on-rectangle">
                                        Check-Out
                                    </x-filament::dropdown.list.item>
                                @endif
                                <x-filament::dropdown.list.item wire:click="bookingSummaryAction('add-payment')"
                                    icon="heroicon-m-currency-dollar">
                                    Add Payment
                                </x-filament::dropdown.list.item>
                                <x-filament::dropdown.list.item wire:click="bookingSummaryAction('add-charge')"
                                    icon="heroicon-m-plus-circle">
                                    Add Charge
                                </x-filament::dropdown.list.item>
                                <x-filament::dropdown.list.item wire:click="bookingSummaryAction('move-room')"
                                    icon="heroicon-m-arrows-right-left">
                                    Move Room
                                </x-filament::dropdown.list.item>
                                <x-filament::dropdown.list.item wire:click="bookingSummaryAction('cancel')"
                                    icon="heroicon-m-x-mark">
                                    Cancel
                                </x-filament::dropdown.list.item>

                                <x-filament::dropdown.list.item wire:click="bookingSummaryAction('void')"
                                    icon="heroicon-m-x-circle">
                                    Void
                                </x-filament::dropdown.list.item>
                            </x-filament::dropdown.list>
                        </x-filament::dropdown>
                    @endif
                </div>
                <table class="w-full">
                    <tr>
                        <td class="px-2 py-3">
                            <div class="text-xs">Booking Type</div>
                            <div class="text-sm font-medium">{{ $booking?->booking_type->name }}</div>
                        </td>
                        <td class="px-2 py-3">
                            <div class="text-xs">Booking Source</div>
                            <div class="text-sm font-medium">
                                @if ($booking?->booking_type->value == 'direct')
                                    {{ $booking?->booking_type_reference ?? '-' }}
                                @else
                                    {{ $booking?->businessSource->name }}
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3">
                            <div class="text-xs">Booking Number</div>
                            <div class="text-sm font-medium">{{ $booking?->booking_number }}</div>
                        </td>
                        <td class="px-2 py-3">
                            <div class="text-xs">Status</div>
                            <div
                                class="text-sm font-medium inline-flex  px-2 py-0.5 rounded {{ $this->selectedFolio?->status->getColor() }}">
                                {{ $this->selectedFolio?->status->name }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3">
                            <div class="text-xs">Arrival Date</div>
                            <div class="text-sm font-medium">{{ $this->selectedFolio?->from->format('jS M Y') }}</div>
                        </td>
                        <td class="px-2 py-3">
                            <div class="text-xs">Departure Date</div>
                            <div class="text-sm font-medium">{{ $this->selectedFolio?->to->format('jS M Y') }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3">
                            <div class="text-xs">Booking Date</div>
                            <div class="text-sm font-medium">
                                {{ $this->selectedFolio?->created_at->format('jS M Y | H:i') }}
                            </div>
                        </td>
                        <td class="px-2 py-3">
                            <div class="text-xs">Room Category</div>
                            <div class="text-sm font-medium">{{ $this->selectedFolio?->room->roomType->name }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3">
                            <div class="text-xs">Room Number</div>
                            <div class="text-sm font-medium">{{ $this->selectedFolio?->room->room_number }}</div>
                        </td>
                        <td class="px-2 py-3">
                            <div class="text-xs">Rate Plan</div>
                            <div class="text-sm font-medium">{{ $this->selectedFolio?->ratePlan->code }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3">
                            <div class="text-xs">Nightly Rate</div>
                            <div class="text-sm font-medium">
                                {{ number_format($this->selectedFolio?->averageRate(), 2) }}
                            </div>
                        </td>
                        <td class="px-2 py-3">
                            <div class="text-xs">Pax</div>
                            <div class="flex items-end space-x-3 text-sm">
                                <div class="flex items-end">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.2 0.800003C8.9875 0.800003 8 1.7875 8 3C8 4.2125 8.9875 5.2 10.2 5.2C11.4125 5.2 12.4 4.2125 12.4 3C12.4 1.7875 11.4125 0.800003 10.2 0.800003ZM8.2 5.6C6.9875 5.6 6 6.5875 6 7.8V12.8C6 13.2422 6.35938 13.6 6.8 13.6C7.24063 13.6 7.6 13.2422 7.6 12.8V8.9875C7.6 8.87969 7.69219 8.7875 7.8 8.7875C7.90781 8.7875 8 8.87969 8 8.9875V18.125C8 18.75 8.33594 19.2 8.9625 19.2C9.55469 19.2 10 18.7406 10 18.125V12.9875C10 12.8766 10.0891 12.7875 10.2 12.7875C10.3109 12.7875 10.4 12.8766 10.4 12.9875V18.2375C10.4016 18.2406 10.4109 18.2344 10.4125 18.2375C10.4672 18.7906 10.8844 19.2 11.4375 19.2C12.0625 19.2 12.4 18.75 12.4 18.125V9.0625C12.4 8.95469 12.4922 8.8625 12.6 8.8625C12.7078 8.8625 12.8 8.95469 12.8 9.0625V12.8C12.8 13.2422 13.1594 13.6 13.6 13.6C14.0406 13.6 14.4 13.2422 14.4 12.8V7.8C14.4 6.5875 13.4125 5.6 12.2 5.6H8.2Z"
                                            fill="currentColor" />
                                    </svg>
                                    <span class="">{{ $this->selectedFolio?->adults }}</span>
                                </div>
                                <div class="flex items-end ">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.1215 4.2C9.1111 4.2 8.29221 5.01888 8.29221 6.02927C8.29221 7.03965 9.1111 7.85853 10.1215 7.85853C11.1319 7.85853 11.9508 7.03965 11.9508 6.02927C11.9508 5.01888 11.1319 4.2 10.1215 4.2ZM9.52554 8.59024C9.04679 8.59024 8.62377 8.80461 8.3308 9.13616L5.25248 11.9787C4.93379 12.2745 4.91378 12.7733 5.20961 13.0934C5.50401 13.4121 6.00277 13.4321 6.32289 13.1363L7.92636 11.6557V13.8951L7.62768 18.2239C7.59052 18.7527 8.00925 19.2 8.53802 19.2C9.0182 19.2 9.4155 18.8284 9.44837 18.3497L9.66702 15.1756H10.5759L10.7946 18.3497C10.8275 18.8284 11.2248 19.2 11.7049 19.2C12.2337 19.2 12.6524 18.7527 12.6153 18.2239L12.3166 13.8908V11.6557L13.9201 13.1363C14.2402 13.4321 14.739 13.4121 15.0334 13.0934C15.3292 12.7733 15.3092 12.2745 14.9905 11.9787L11.9122 9.13616C11.6192 8.80461 11.1962 8.59024 10.7174 8.59024H9.52554Z"
                                            fill="currentColor" />
                                    </svg>

                                    <span class="">{{ $this->selectedFolio?->children }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3">
                            <div class="text-xs">Pending Payment</div>
                            <div @class([
                                'text-sm font-medium',
                                'text-red-500' => $reservationTotals > 0,
                            ])>
                                {{ number_format($reservationTotals['balance'] ?? 0, 2) }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            @if ($booking?->bookingReservations->where('status', '!=', App\Enums\Status::Maintenance)->count() > 1)
                <div class="w-2/5 px-3 border-l">
                    <div class="mb-2 text-sm font-semibold">Reservations</div>
                    <div class="space-y-3 ">
                        @php
                            $bookingOtherReservations = $booking?->bookingReservations->where(
                                'status',
                                '!=',
                                App\Enums\Status::Maintenance,
                            );
                        @endphp
                        @foreach ($bookingOtherReservations as $bookingReservations)
                            <x-filament::button wire:key="booking-reservation-{{ $bookingReservations->id }}"
                                wire:click="$set('reservation_id', {{ $bookingReservations->id }})" color="gray"
                                @class([
                                    'border w-full !flex-col !items-start !justify-start text-left',
                                    'border-blue-600' => $bookingReservations->id == $this->selectedFolio->id,
                                ])>
                                <div class="font-semibold ">{{ $bookingReservations->booking_customer }}</div>
                                <div class="text-xs">
                                    <div>{{ $bookingReservations->room->roomType->name }} -
                                        {{ $bookingReservations->room->room_number }}
                                    </div>
                                    <div>{{ $bookingReservations?->booking_number }}-{{ $loop->iteration }}</div>
                                </div>
                            </x-filament::button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-filament::modal>

    <x-filament-actions::modals />

</div>
