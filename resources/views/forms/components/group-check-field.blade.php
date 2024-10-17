<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        @foreach (collect($getOptions()) as $key => $option)
            <div>
                @php
                    $reservation = App\Models\BookingReservation::find($key);
                    $disabled = false;
                    if ($getType() == 'check-out' && reservationTotals($reservation->id)['balance'] > 0) {
                        $disabled = true;
                    }
                @endphp
                <label>
                    <x-filament::input.checkbox x-model="state" value="{{ $key }}" :disabled="$disabled" />
                    <span @class(['text-sm px-2', 'opacity-50' => $disabled])>
                        {{ $option }}
                    </span>
                    @if (reservationTotals($reservation->id)['balance'] > 0)
                        <span class="text-sm text-red-600">[pending payment -
                            {{ number_format(reservationTotals($reservation->id)['balance'], 2) }}]</span>
                    @endif
                </label>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
