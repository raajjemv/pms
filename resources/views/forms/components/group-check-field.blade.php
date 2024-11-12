<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        @foreach (collect($getOptions()) as $key => $option)
            <div>
                @php
                    $reservation = App\Models\BookingReservation::withCount('customers')->find($key);
                    $disabled = false;
                    if (
                        (in_array($getType(), ['check-out', 'early-check-out']) &&
                            reservationTotals($reservation->id)['balance'] > 0) ||
                        $reservation->status->value == 'check-out'
                    ) {
                        $disabled = true;
                    }
                    if (
                        $reservation->status->value == 'check-in' &&
                        $getType() == 'early-check-out' &&
                        $reservation->to->lt(now())
                    ) {
                        $disabled = true;
                    }
                    if ($reservation->customers_count < $reservation->totalPax()) {
                        $disabled = true;
                    }
                @endphp
                <label>
                    <x-filament::input.checkbox x-model="state" value="{{ $key }}" :disabled="$disabled" />
                    <span @class([
                        'text-sm px-2',
                        'opacity-50' => $disabled,
                        'line-through' => $reservation->status->value == 'check-out',
                    ])>
                        {{ $option }}
                    </span>
                    @if (reservationTotals($reservation->id)['balance'] > 0)
                        <span class="text-sm text-red-600">[pending payment -
                            {{ number_format(reservationTotals($reservation->id)['balance'], 2) }}]</span>
                    @endif
                    @if ($reservation->status->value == 'check-out')
                        <span class="text-sm text-red-600">Checked-Out</span>
                    @endif
                </label>
                @if ($reservation->customers_count < $reservation->totalPax())
                    <div class="mt-1 text-sm text-gray-500 pl-7">Guest Details Missing</div>
                @endif
                @if (
                    $reservation->status->value == 'check-in' &&
                        $getType() == 'early-check-out' &&
                        $reservation->to->gt(now()) &&
                        !$disabled)
                    <div class="mt-1 text-sm text-gray-500 pl-7">
                        [
                        {{ implode(', ',totolNightsByDates(now(), $reservation->to)->map(fn($date) => $date->format('d/m/Y'))->toArray()) }}]
                        - night(s) to adjust
                    </div>
                @endif

            </div>
        @endforeach
    </div>
</x-dynamic-component>
