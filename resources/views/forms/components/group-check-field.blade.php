<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        @foreach (collect($getOptions()) as $key => $option)
            <div>
                @php
                    $reservation = App\Models\BookingReservation::find($key);
                    $disabled = false;
                    if (!$reservation->from->isToday()) {
                        $disabled = true;
                    }
                @endphp
                <label>
                    <x-filament::input.checkbox x-model="state" value="{{ $key }}" :disabled="$disabled" />
                    <span @class(['text-sm px-2', 'text-red-600 opacity-50' => $disabled])>
                        {{ $option }}
                    </span>
                </label>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
