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



    @switch($activeTab)
        @case('guest-accounting')
            {{-- @livewire('pms.reservation.guest-accounting', ['booking' => $booking]) --}}
            <livewire:pms.reservation.guest-accounting :booking="$booking" @refresh-edit-reservation="$refresh">
            @break

            @case('booking-details')
            @break

            @case('guest-profile')
                <livewire:pms.reservation.guest-profiles :booking="$booking" />
            @break

            @case('room-charges')
                <div>rc</div>
            @break

            @default
        @endswitch

</x-filament-panels::page>
