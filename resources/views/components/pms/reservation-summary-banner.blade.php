@props(['reservation'])
@php
    $status = $reservation->status;
@endphp
<div class="w-full my-3">
    <div class="flex flex-row-reverse mb-1">
        @if (
            ($reservation->from->isToday() || $reservation->from->isPast()) &&
                in_array($reservation->status->value, ['reserved', 'inquiry', 'hold', 'confirmed', 'paid']))
            {{ $this->checkInAction }}
        @endif
        @if (
            ($reservation->to->isToday() || $reservation->to->isPast()) &&
                in_array($reservation->status->value, ['check-in', 'overstay']))
            {{ $this->checkOutAction }}
        @endif
    </div>
    <div class="grid items-center min-w-full grid-cols-2 gap-3 px-2 py-4 text-sm rounded-lg lg:grid-cols-5 bg-slate-100">
        <div class="flex items-center space-x-2">
            <div class="font-thin text-gray-700">Arrival Date</div>
            <div class="">{{ $reservation->from->format('d/m/Y H:i') }}</div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="font-thin text-gray-700">Depature Date</div>
            <div class="">{{ $reservation->to->format('d/m/Y H:i') }}</div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="font-thin text-gray-700">Room</div>
            <div class="">{{ $reservation?->room?->roomType?->name }} - {{ $reservation?->room?->room_number }}
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="font-thin text-gray-700">Nights</div>
            <div class="">{{ totolNights($reservation->from, $reservation->to) }}</div>
        </div>
        <div>
            <div class="{{ $status->getColor() }} inline-flex px-2 py-1 rounded text-sm font-medium capitalize">
                Status: {{ $status->value }}
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="font-thin text-gray-700">Check-In</div>
            <div class="">{{ $reservation->check_in }}</div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="font-thin text-gray-700">Check-Out</div>
            <div class="">{{ $reservation->check_out ?? '-' }}</div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="font-thin text-gray-700">Rate Plan</div>
            <div class="">{{ $reservation->ratePlan->code }}</div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="font-thin text-gray-700">Pax</div>
            <div class="flex items-end space-x-3 text-sm">
                <div class="flex items-end">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.2 0.800003C8.9875 0.800003 8 1.7875 8 3C8 4.2125 8.9875 5.2 10.2 5.2C11.4125 5.2 12.4 4.2125 12.4 3C12.4 1.7875 11.4125 0.800003 10.2 0.800003ZM8.2 5.6C6.9875 5.6 6 6.5875 6 7.8V12.8C6 13.2422 6.35938 13.6 6.8 13.6C7.24063 13.6 7.6 13.2422 7.6 12.8V8.9875C7.6 8.87969 7.69219 8.7875 7.8 8.7875C7.90781 8.7875 8 8.87969 8 8.9875V18.125C8 18.75 8.33594 19.2 8.9625 19.2C9.55469 19.2 10 18.7406 10 18.125V12.9875C10 12.8766 10.0891 12.7875 10.2 12.7875C10.3109 12.7875 10.4 12.8766 10.4 12.9875V18.2375C10.4016 18.2406 10.4109 18.2344 10.4125 18.2375C10.4672 18.7906 10.8844 19.2 11.4375 19.2C12.0625 19.2 12.4 18.75 12.4 18.125V9.0625C12.4 8.95469 12.4922 8.8625 12.6 8.8625C12.7078 8.8625 12.8 8.95469 12.8 9.0625V12.8C12.8 13.2422 13.1594 13.6 13.6 13.6C14.0406 13.6 14.4 13.2422 14.4 12.8V7.8C14.4 6.5875 13.4125 5.6 12.2 5.6H8.2Z"
                            fill="currentColor" />
                    </svg>
                    <span class="">{{ $reservation?->adults }}</span>
                </div>
                <div class="flex items-end ">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.1215 4.2C9.1111 4.2 8.29221 5.01888 8.29221 6.02927C8.29221 7.03965 9.1111 7.85853 10.1215 7.85853C11.1319 7.85853 11.9508 7.03965 11.9508 6.02927C11.9508 5.01888 11.1319 4.2 10.1215 4.2ZM9.52554 8.59024C9.04679 8.59024 8.62377 8.80461 8.3308 9.13616L5.25248 11.9787C4.93379 12.2745 4.91378 12.7733 5.20961 13.0934C5.50401 13.4121 6.00277 13.4321 6.32289 13.1363L7.92636 11.6557V13.8951L7.62768 18.2239C7.59052 18.7527 8.00925 19.2 8.53802 19.2C9.0182 19.2 9.4155 18.8284 9.44837 18.3497L9.66702 15.1756H10.5759L10.7946 18.3497C10.8275 18.8284 11.2248 19.2 11.7049 19.2C12.2337 19.2 12.6524 18.7527 12.6153 18.2239L12.3166 13.8908V11.6557L13.9201 13.1363C14.2402 13.4321 14.739 13.4121 15.0334 13.0934C15.3292 12.7733 15.3092 12.2745 14.9905 11.9787L11.9122 9.13616C11.6192 8.80461 11.1962 8.59024 10.7174 8.59024H9.52554Z"
                            fill="currentColor" />
                    </svg>

                    <span class="">{{ $reservation?->children }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
