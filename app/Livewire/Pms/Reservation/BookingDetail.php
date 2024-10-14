<?php

namespace App\Livewire\Pms\Reservation;

use App\Enums\BookingType;
use App\Models\BusinessSource;
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
            'billing_customer_email' => $this->booking->billing_customer_email,
            'booking_type' => $this->booking->booking_type,
            'booking_type_reference' => $this->booking->booking_type_reference,
            'business_source_id' => $this->booking->business_source_id
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('billing_customer')
                    ->required(),
                Forms\Components\TextInput::make('billing_customer_email'),

                Forms\Components\Select::make('booking_type')
                    ->options(BookingType::class)
                    ->live(),

                Forms\Components\Select::make('business_source_id')
                    ->label('Business Source')
                    ->options(BusinessSource::orderBy('name', 'ASC')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn($get) => $get('booking_type')->value !== 'direct'),

                Forms\Components\Select::make('booking_type_reference')
                    ->options([
                        'walk-in' => 'Walk-In',
                        'phone' => 'Phone',
                        'website' => 'Website',
                        'email' => 'Email'
                    ])
                    ->visible(fn($get) => $get('booking_type')->value == 'direct'),


            ])
            ->columns(2)
            ->statePath('data')
            ->model($this->booking);
    }

    public function saveBookingDetail()
    {
        $this->booking->forceFill($this->form->getState())->save();

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
