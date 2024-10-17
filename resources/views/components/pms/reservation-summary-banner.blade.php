@props(['reservation'])
@php
    $status = $reservation->status;
@endphp
<div>
    <div class="flex flex-row-reverse mb-1">
        @if (($reservation->from->isToday() || $reservation->from->isPast()) &&
                in_array($reservation->status->value, ['reserved', 'inquiry', 'hold', 'confirmed', 'paid']))
            {{ $this->checkInAction }}
        @endif
        @if (
            ($reservation->to->isToday() || $reservation->to->isPast()) &&
                in_array($reservation->status->value, ['check-in', 'overstay']))
            {{ $this->checkOutAction }}
        @endif
    </div>
    <div class="grid items-center min-w-full grid-cols-5 gap-3 p-2 text-sm rounded-lg bg-slate-100">
        <div class="space-y-2">
            <div class="font-bold ">Arrival Date</div>
            <div>{{ $reservation->from->format('d/m/Y H:i') }}</div>
        </div>
        <div class="space-y-2">
            <div class="font-bold ">Depature Date</div>
            <div>{{ $reservation->to->format('d/m/Y H:i') }}</div>
        </div>
        <div class="space-y-2">
            <div class="font-bold">Room</div>
            <div>
                <div>{{ $reservation?->room?->roomType?->name }} - {{ $reservation?->room?->room_number }}</div>
            </div>
        </div>
        <div class="space-y-2">
            <div class="font-bold ">Nights</div>
            <div>{{ totolNights($reservation->from, $reservation->to) }}</div>
        </div>
        <div>
            <div class="{{ $status->getColor() }} inline-flex px-2 py-1 rounded text-sm font-medium capitalize">
                Status: {{ $status->value }}
            </div>
        </div>
    </div>
</div>
