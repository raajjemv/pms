<?php

namespace App\Livewire\Pms\Reservation;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use App\Enums\PaymentType;
use Livewire\Attributes\Reactive;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
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

    public $invoiceName = '';

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
        $file_name = str()->random(15) . '.pdf';

        $this->invoiceName = $file_name;


        $d = pdf()
            ->view('pdf.reservation-invoice', [
                'booking' => $this->booking,
                'reservation' => $this->selectedFolio,
                'paid' => $this->booking
                    ->bookingTransactions
                    ->where('booking_reservation_id', $this->selectedFolio->id)
                    ->whereIn('transaction_type', PaymentType::getAllValues())
                    ->sum('rate')
            ])
            ->footerView('footer-view')
            ->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot->setChromePath(env('CHROME_PATH'))
                    ->transparentBackground();
            })

            ->format(Format::A4)
            // ->disk(env('FILESYSTEM_DISK'))
            ->disk('public')
            ->save('reservation-invoices/' . $file_name);

        $this->dispatch('open-modal', id: 'reservation-invoice');
    }

    public function render()
    {
        return view('livewire.pms.reservation.print-email');
    }
}
