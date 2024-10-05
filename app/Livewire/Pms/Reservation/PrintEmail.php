<?php

namespace App\Livewire\Pms\Reservation;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use Livewire\Attributes\Reactive;
use Spatie\Browsershot\Browsershot;
use Filament\Forms\Contracts\HasForms;
use function Spatie\LaravelPdf\Support\pdf;
use Filament\Forms\Concerns\InteractsWithForms;

class PrintEmail extends Component implements HasForms
{

    use InteractsWithForms;

    public $booking;

    // #[Reactive]
    public $selectedFolio;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'email' => $this->booking->billing_customer_email ?? $this->booking->booking_email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('email')
                    ->required(),
            ])
            ->statePath('data')
            ->model($this->booking);
    }

    public function printInvoice()
    {
        $d = pdf()
            ->view('pdf.reservation-invoice', [
                'booking' => $this->booking,
                'reservation' => $this->selectedFolio
            ])
            ->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot->setChromePath(env('CHROME_PATH'));
            })
            ->disk('public')
            ->save('reservation-invoices/invoice-2023-04-10.pdf');

        $this->dispatch('open-modal', id: 'edit-user');
    }

    public function render()
    {
        return view('livewire.pms.reservation.print-email');
    }
}
