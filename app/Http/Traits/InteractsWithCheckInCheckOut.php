<?php

namespace App\Http\Traits;

use Filament\Actions\Action;
use App\Models\BookingReservation;
use Filament\Notifications\Notification;
use App\Forms\Components\GroupCheckField;

trait InteractsWithCheckInCheckOut
{
    public function checkInAction(): Action
    {
        return Action::make('checkIn')
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
        return Action::make('checkOut')
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
    
    
}
