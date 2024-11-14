<div>
    <form wire:submit="saveReservationDetail">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-3">
            Save
        </x-filament::button>
    </form>
</div>
