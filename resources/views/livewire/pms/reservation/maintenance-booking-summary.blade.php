<div>
    @php
        $reservationTotals = $this->selectedFolio ? reservationTotals($this->selectedFolio?->id) : 0;
    @endphp
    <x-filament::modal :close-by-clicking-away="false" :autofocus="false" closeEventName="close-maintenance-booking-summary-modal"
        id="maintenance-booking-summary" slide-over width="sm">
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

        @if ($this->selectedFolio)
            <form wire:submit='saveReservation'>
                {{ $this->form }}

                <div class="flex items-start justify-between mt-5">
                    <x-filament::button type="submit">
                        Save
                    </x-filament::button>

                    <div class="w-1/2 text-right">
                        {{ $this->unblock }}
                        @if ($booking->bookingReservations->where('status', '!=', App\Enums\Status::Maintenance)->count() > 0)
                            <div class="text-sm text-red-600">Cannot unblock room. Connected Rooms are in use!</div>
                        @endif

                    </div>
                </div>
            </form>
        @endif


    </x-filament::modal>

    <x-filament-actions::modals />

</div>
