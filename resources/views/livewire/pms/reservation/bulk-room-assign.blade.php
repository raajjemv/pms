<x-filament::modal :close-by-clicking-away="false" closeEventName="close-bulk-room-assign-modal" id="bulk-room-assign-modal" width="7xl">
    <x-slot name="heading">
        Room Assign
    </x-slot>
    <div>
        <form wire:submit="save">
            {{ $this->form }}

            <x-filament::button type="submit" class="mt-4">
                Save
            </x-filament::button>
        </form>

    </div>
</x-filament::modal>
