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
    </div>
</div>
