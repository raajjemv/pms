<x-filament::modal :close-by-clicking-away="false" closeEventName="close-reservation-modal" id="reservation-modal" 
    width="screen">
    <x-slot name="heading">
        <div class="flex flex-wrap items-center justify-between space-y-3 lg:space-y-0">

            <div class="text-2xl">
                <div>{{ $this->reservation?->booking_customer }}</div>
                <div class="text-sm text-gray-600">{{ $booking?->booking_number }} |
                    {{ $this->reservation?->created_at->format('jS M Y H:i') }}</div>
            </div>

            <div class="flex-grow lg:flex-grow-0">
                <div class="grid grid-cols-2 gap-4 0 lg:pr-10">
                    <div class="px-10 py-3 space-y-1 text-center border rounded-lg">
                        <div class="text-2xl font-bold">
                            {{ number_format(reservationTotals($this->reservation?->id)['total'] ?? 0, 2) ?? '-' }}
                        </div>
                        <div class="text-sm font-thin text-gray-600">Total Payable</div>
                    </div>
                    <div class="px-10 py-3 space-y-1 text-center border rounded-lg">
                        <div class="text-2xl font-bold text-red-700">
                            {{ number_format(reservationTotals($this->reservation?->id)['balance'] ?? 0, 2) ?? '-' }}
                        </div>
                        <div class="text-sm font-thin text-red-600">Balance</div>
                    </div>
                </div>
            </div>
        </div>
        @if ($this->reservation)
            <x-pms.reservation-summary-banner :reservation="$this->reservation" />
        @endif
    </x-slot>

    <div class="max-h-full overflow-scroll ">
     
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


        <div class="grid grid-cols-1 gap-3 mt-5 lg:grid-cols-5">
            <x-filament::section class="" :headerActions="[$this->addFolioAccountAction()]">
                <x-slot name="description">
                    Folio Accounts
                </x-slot>
                @if ($this->reservation)
                    <div class="space-y-3">
                        @foreach ($booking->bookingReservations as $reservation)
                            <div class="flex flex-col">
                                <button wire:key="reservation-{{ $reservation->id }}"
                                    wire:click="$set('reservation_id',{{ $reservation->id }})"
                                    @class([
                                        'text-sm text-left font-medium',
                                        'text-blue-500' => $this->reservation->id == $reservation->id,
                                        'text-gray-500' => $this->reservation->id != $reservation->id,
                                    ])>
                                    <i class="pr-2 fas fa-minus"></i> {{ $reservation->booking_customer }}
                                </button>
                                <div class="text-xs text-gray-600">
                                    {{ $reservation->room->roomType->name }} -
                                    {{ $reservation->room->room_number }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </x-filament::section>
            @if ($booking)
                <div class="col-span-4">
                    @switch($activeTab)
                        @case('guest-accounting')
                            <livewire:pms.reservation.guest-accounting :booking="$booking" @refresh-edit-reservation="$refresh"
                                :selected-folio="$this->reservation">
                            @break

                            @case('booking-detail')
                                <livewire:pms.reservation.booking-detail :booking="$booking" :selected-folio="$this->reservation" />
                            @break

                            @case('guest-profile')
                                <livewire:pms.reservation.guest-profiles :booking="$booking" :selected-folio="$this->reservation" />
                            @break

                            @case('print-email')
                                <livewire:pms.reservation.print-email :booking="$booking" :selected-folio="$this->reservation" />
                            @break

                            @default
                        @endswitch
                </div>
            @endif
        </div>

    </div>

    <style>
        .fi-modal-header>div:nth-child(2) {
            width: 100%;
        }
        .fi-modal-content{
            overflow: scroll;
        }
        
    </style>
        <x-filament-actions::modals />
</x-filament::modal>
