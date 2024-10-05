<x-filament::section>
    <x-slot name="heading">
        User details
    </x-slot>

    <div>
        <x-filament::button color="success" wire:click="printInvoice" icon="heroicon-m-printer">
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

    <x-filament::modal id="edit-user" width="screen">
        <x-slot name="heading">
            Invoice
        </x-slot>
        <iframe src="{{ asset('reservation-invoices/invoice-2023-04-10.pdf') }}" frameborder="0"
            class="h-full "></iframe>
    </x-filament::modal>
</x-filament::section>
