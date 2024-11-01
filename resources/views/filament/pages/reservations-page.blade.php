<x-filament-panels::page wire:poll.60s>
    <div class="flex items-center justify-between">
        <div>
            <x-filament::tabs label="Reservation tabs">

                <x-filament::tabs.item icon="heroicon-m-arrow-down-on-square" :active="$activeTab == 'todays-reservations'"
                    wire:click="setTableQuery('todays-reservations')">
                    Todays Reservations
                    <x-slot name="badge">
                        {{ $this->todaysRevervations->count() }}
                    </x-slot>
                </x-filament::tabs.item>

                <x-filament::tabs.item icon="heroicon-m-arrow-left-end-on-rectangle" :active="$activeTab == 'arrivals'"
                    wire:click="setTableQuery('arrivals')">
                    Arrivals
                    <x-slot name="badge">
                        {{ $this->arrivalReservations->count() }}
                    </x-slot>
                </x-filament::tabs.item>

                <x-filament::tabs.item icon="heroicon-m-arrow-right-start-on-rectangle" :active="$activeTab == 'departures'"
                    wire:click="setTableQuery('departures')">
                    Departures
                    <x-slot name="badge">
                        {{ $this->departureReservations->count() }}
                    </x-slot>
                </x-filament::tabs.item>

                <x-filament::tabs.item icon="heroicon-m-arrow-path-rounded-square" :active="$activeTab == 'active-stays'"
                    wire:click="setTableQuery('active-stays')">
                    Active Stays
                    <x-slot name="badge">
                        {{ $this->activeReservations->count() }}
                    </x-slot>
                </x-filament::tabs.item>

            </x-filament::tabs>
        </div>
        <div class="text-sm text-gray-600" wire:loading>Loading <i class="fas fa-circle-notch fa-spin"></i></div>
    </div>

    {{ $this->table }}



    <livewire:pms.reservation.booking-summary />

    <livewire:pms.reservation.reservation />
</x-filament-panels::page>
