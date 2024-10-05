<?php

namespace App\Livewire\Pms\Reservation;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use Livewire\Attributes\Reactive;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class BookingDetail extends Component implements HasForms
{
    use InteractsWithForms;

    public $booking;

    #[Reactive]
    public $selectedFolio;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'billing_customer' => $this->booking->billing_customer ?? $this->booking->booking_customer,
            'billing_customer_email' => $this->booking->billing_customer_email
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('billing_customer')
                    ->required(),
                Forms\Components\TextInput::make('billing_customer_email')


            ])
            ->statePath('data')
            ->model($this->booking);
    }

    public function saveBookingDetail()
    {
        $this->booking->forceFill($this->form->getState())->save();

        $this->form->fill([]);

        Notification::make()
            ->title('Booking Details Updated!')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.pms.reservation.booking-detail');
    }
}
