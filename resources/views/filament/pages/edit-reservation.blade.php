<x-filament-panels::page>
    <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item :active="$activeTab === 'guest-accounting'" wire:click="$set('activeTab', 'guest-accounting')">
            Guest Accounting
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$activeTab === 'booking-details'" wire:click="$set('activeTab', 'booking-details')">
            Booking Details
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$activeTab === 'guest-profile'" wire:click="$set('activeTab', 'guest-profile')">
            Guest Profile
        </x-filament::tabs.item>
        <x-filament::tabs.item :active="$activeTab === 'room-charges'" wire:click="$set('activeTab', 'room-charges')">
            Room Charges
        </x-filament::tabs.item>
    </x-filament::tabs>


    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">

        <x-filament::section :headerActions="[$this->addFolioAccountAction()]">
            <x-slot name="description">
                Folio Accounts
            </x-slot>
            <div class="space-y-3">
                @foreach ($booking->bookingReservations as $reservation)
                    <div>
                        <button wire:key="reservation-{{ $reservation->id }}"
                            wire:click="setSelectedFolio({{ $reservation->id }})" @class([
                                'text-sm text-gray-700 ',
                                'font-bold text-blue-600' => $selectedFolio->id == $reservation->id,
                            ])>
                            <i class="pr-2 fas fa-minus"></i> {{ $reservation->booking_customer }}
                        </button>
                        <div class="text-xs text-gray-600">
                            {{ $reservation->room->roomType->name }} - {{ $reservation->room->room_number }}
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
        <div class="col-span-4">
            @switch($activeTab)
                @case('guest-accounting')
                    {{-- @livewire('pms.reservation.guest-accounting', ['booking' => $booking]) --}}
                    <livewire:pms.reservation.guest-accounting :booking="$booking" @refresh-edit-reservation="$refresh"
                        :selected-folio="$selectedFolio">
                    @break

                    @case('booking-details')
                    @break

                    @case('guest-profile')
                        <livewire:pms.reservation.guest-profiles :booking="$booking"  :selected-folio="$selectedFolio"/>
                    @break

                    @case('room-charges')
                        <div>rc</div>
                    @break

                    @default
                @endswitch
        </div>
    </div>


</x-filament-panels::page>
