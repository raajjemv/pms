<?php

namespace App\Http\Traits;

use Filament\Forms;
use App\Enums\PaymentType;
use Filament\Actions\Action;
use App\Models\BookingReservation;
use App\Models\BookingTransaction;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use App\Forms\Components\GroupCheckField;

trait InteractsWithReservationActions
{
    use CachedQueries;
    public function checkInAction(): Action
    {
        return Action::make('checkInAction')
            ->icon('heroicon-m-check-circle')
            ->color('gray')
            ->form(function () {
                return [
                    GroupCheckField::make('reservations')
                        ->type('check-in')
                        ->options(fn() => $this->booking->bookingReservations->pluck('booking_customer', 'id'))
                        ->required()
                        ->validationMessages([
                            'required' => 'Select a reservation to proceed!',
                        ])
                ];
            })
            ->action(function ($data) {
                collect($data['reservations'])->each(function ($reservation_id) {
                    $reservation = BookingReservation::find($reservation_id);
                    $reservation->check_in = now();
                    $reservation->status = 'check-in';
                    $reservation->save();

                    activity()->performedOn($reservation)->log('Check-In Processed');
                });
                $this->dispatch('refresh-scheduler');
                Notification::make()
                    ->title('Check-In Successfull!')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }
    public function checkOutAction(): Action
    {
        return Action::make('checkOutAction')
            ->icon('heroicon-m-check-circle')
            ->color('gray')
            ->form(function () {
                return [
                    GroupCheckField::make('reservations')
                        ->type('check-out')
                        ->options(fn() => $this->booking->bookingReservations->pluck('booking_customer', 'id'))
                        ->required()
                        ->validationMessages([
                            'required' => 'Select a reservation to proceed!',
                        ])
                ];
            })
            ->action(function ($data) {
                collect($data['reservations'])->each(function ($reservation_id) {
                    $reservation = BookingReservation::find($reservation_id);
                    $reservation->check_out = now();
                    $reservation->status = 'check-out';
                    $reservation->save();

                    activity()->performedOn($reservation)->log('Check-Out Processed');
                });
                $this->dispatch('refresh-scheduler');
                Notification::make()
                    ->title('Check-Out Successfull!')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }
    public function addPaymentAction()
    {
        return Action::make('addPaymentAction')
            ->modalSubmitActionLabel('Add')
            ->icon('heroicon-m-currency-dollar')
            ->color('gray')
            ->fillForm(fn(): array => [
                'rate' => reservationTotals($this->reservation->id)['balance'],
                'folio' => $this->reservation->booking_customer
            ])
            ->mutateFormDataUsing(function (array $data): array {
                $data['booking_id'] = $this->booking->id;
                $data['user_id'] = auth()->id();
                $data['booking_reservation_id'] = $this->reservation->id;
                return $data;
            })
            ->modalWidth(MaxWidth::Small)
            ->form([
                Forms\Components\TextInput::make('folio')
                    ->dehydrated(false)
                    ->disabled(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('rate')
                    ->label('Amount')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('transaction_type')
                    ->options(PaymentType::class)
                    ->required()
                    ->live(),

                Forms\Components\Select::make('business_source_id')
                    ->label('Business Source')
                    ->options(static::businessSources()->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn($get) => $get('transaction_type') == 'city_ledger')
                    ->required(fn($get) => $get('transaction_type') == 'city_ledger' ? true : false),

            ])
            ->action(function ($data) {
                BookingTransaction::create($data);
            })
            ->after(function ($data) {
                Cache::forget('reservationBalance_' . $this->reservation->id);
                activity()->performedOn($this->reservation)->log('Payment of ' . $data['rate'] . ' collected by ' . $data['transaction_type']);
                $this->dispatch('refresh-scheduler');
            });
    }
}
