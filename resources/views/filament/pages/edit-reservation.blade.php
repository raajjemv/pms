<x-filament-panels::page>
    <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item :active="$activeTab === 'guest-accounting'" wire:click="$set('activeTab', 'guest-accounting')">
            Guest Accounting
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$activeTab === 'booking-details'" wire:click="$set('activeTab', 'booking-details')">
            Booking Details
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$activeTab === 'guest-details'" wire:click="$set('activeTab', 'guest-details')">
            Guest Profile
        </x-filament::tabs.item>
        <x-filament::tabs.item :active="$activeTab === 'room-charges'" wire:click="$set('activeTab', 'room-charges')">
            Room Charges
        </x-filament::tabs.item>
    </x-filament::tabs>
</x-filament-panels::page>
