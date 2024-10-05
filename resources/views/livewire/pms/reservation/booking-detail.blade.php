<x-filament::section>
  
    <form wire:submit="saveBookingDetail">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-3">
            Save
        </x-filament::button>
    </form>

</x-filament::section>
