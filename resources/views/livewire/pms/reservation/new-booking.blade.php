<div>
    @php
        $from = \Carbon\Carbon::parse($this->from);
        $to = \Carbon\Carbon::parse($this->to);
    @endphp
    <div class="mb-2 font-medium">Total Nights {{ round($from->diffInDays($to)) }}</div>
    <form wire:submit="createBooking">
        {{ $this->form }}

        <x-filament::button color="success" type="submit" class="mt-5">
            Create
        </x-filament::button>
    </form>
</div>
