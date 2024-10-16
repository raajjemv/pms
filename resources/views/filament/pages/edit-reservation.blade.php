<x-filament-panels::page>

    <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item :active="$activeTab === 'guest-accounting'" wire:click="$set('activeTab', 'guest-accounting')">
            Guest Accounting
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$activeTab === 'booking-detail'" wire:click="$set('activeTab', 'booking-detail')">
            Booking Details
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$activeTab === 'guest-profile'" wire:click="$set('activeTab', 'guest-profile')">
            Guest Profile
        </x-filament::tabs.item>
        <x-filament::tabs.item :active="$activeTab === 'print-email'" wire:click="$set('activeTab', 'print-email')">
            Print / Email
        </x-filament::tabs.item>
    </x-filament::tabs>


    <div class="grid grid-cols-1 gap-3 lg:grid-cols-5">

        <x-filament::section class="" :headerActions="[$this->addFolioAccountAction()]">
            <x-slot name="description">
                Folio Accounts
            </x-slot>
            <div class="space-y-3">
                @foreach ($booking->bookingReservations as $reservation)
                    <div class="flex flex-col">
                        <button wire:key="reservation-{{ $reservation->id }}"
                            wire:click="$set('reservation_id',{{ $reservation->id }})" @class([
                                'text-sm text-gray-700 text-left ',
                                'font-bold text-blue-600' => $this->selectedFolio->id == $reservation->id,
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
                    <livewire:pms.reservation.guest-accounting :booking="$booking" @refresh-edit-reservation="$refresh"
                        :selected-folio="$this->selectedFolio">
                    @break

                    @case('booking-detail')
                        <livewire:pms.reservation.booking-detail :booking="$booking" :selected-folio="$this->selectedFolio" />
                    @break

                    @case('guest-profile')
                        <livewire:pms.reservation.guest-profiles :booking="$booking" :selected-folio="$this->selectedFolio" />
                    @break

                    @case('print-email')
                        <livewire:pms.reservation.print-email :booking="$booking" :selected-folio="$this->selectedFolio" />
                    @break

                    @default
                @endswitch
        </div>
    </div>
    <style>
        .fi-header>div {
            width: 100%;
        }
    </style>
    <x-filament-actions::modals />

</x-filament-panels::page>
