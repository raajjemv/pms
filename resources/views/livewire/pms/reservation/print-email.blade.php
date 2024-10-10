<x-filament::section>
    <x-slot name="heading">
        User details
    </x-slot>

    <div>
        <x-filament::button color="success" target="_blank" href="{{ route('pdf.reservation-invoice',['booking_id' => encrypt($booking->id)]) }}" tag="a"
            icon="heroicon-m-printer">
            Print
        </x-filament::button>
    </div>

    <div class="py-1 my-6 font-semibold text-center text-gray-600 border-y">
        OR
    </div>

    <form wire:submit="emailInvoice" class="max-w-sm">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-3">
            Email
        </x-filament::button>
    </form>

    {{-- <x-filament::modal id="reservation-invoice" width="screen">
        <x-slot name="heading">
            Invoice
        </x-slot>

        @if ($invoiceName)
            <iframe src="{{ Storage::disk(env('FILESYSTEM_DISK'))->url('reservation-invoices/' . $invoiceName) }}"
                frameborder="0" class="h-full "></iframe>


    @endif

    </x-filament::modal> --}}
</x-filament::section>
