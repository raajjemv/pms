<?php

namespace App\Filament\ActionsExtended\EarlyCheckOutAction;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use App\Forms\Components\GroupCheckField;

trait EarlyCheckOutActionTrait
{
    use CachedQueries;

    public static function getDefaultName(): ?string
    {
        return 'early-checkout';
    }

    protected function setUp(): void
    {

        parent::setUp();

        $this
            ->icon('heroicon-m-arrow-right-end-on-rectangle')
            ->color('gray')
            ->form(function () {
                return [
                    GroupCheckField::make('reservations')
                        ->type('early-check-out')
                        ->options(fn($livewire) => $livewire->booking->bookingReservations->pluck('booking_customer', 'id'))
                        ->required()
                        ->validationMessages([
                            'required' => 'Select a reservation to proceed!',
                        ])
                ];
            })
            ->requiresConfirmation()
            ->visible(fn($livewire) => $livewire->selectedFolio->status->value == 'check-in' && $livewire->selectedFolio->to->gt(now()));


        $this->action(function ($data, $livewire): void {
            collect($data['reservations'])->each(function ($reservation_id) {
                $reservation = BookingReservation::find($reservation_id);

                $totalNightsLeft = totolNightsByDates(now(), $reservation->to)->map(fn($date) => $date->format('Y-m-d'));


                $updateTransactions = $reservation->bookingTransactions()
                    ->whereIn('date', $totalNightsLeft)
                    ->where('transaction_type', 'room_charge')
                    ->update([
                        'transaction_type' => 'early_Checkout_fee'
                    ]);


                $reservation->to = now()->setTimeFromTimeString(tenant()->check_out_time);
                $reservation->check_out = now();
                $reservation->status = 'check-out';
                $reservation->save();

                activity()->performedOn($reservation)->log('Check-Out Processed');
            });
        });

        $this->after(function ($livewire, $data) {

            Notification::make()
                ->title('Check-Out Successfull!')
                ->success()
                ->send();

            $livewire->dispatch('refresh-scheduler');
            $livewire->dispatch('refresh-edit-reservation');

           
        });
    }
}
