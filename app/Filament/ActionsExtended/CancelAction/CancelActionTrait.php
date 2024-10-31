<?php

namespace App\Filament\ActionsExtended\CancelAction;

use App\Enums\Status;
use Filament\Forms;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use App\Models\BookingTransaction;
use App\Models\CancelReason;
use Filament\Facades\Filament;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Filament\Widgets\StatsOverviewWidget\Stat;

trait CancelActionTrait
{
    use CachedQueries;

    public static function getDefaultName(): ?string
    {
        return 'cancel-reservation';
    }

    protected function setUp(): void
    {

        parent::setUp();

        $reasons = CancelReason::pluck('reason', 'id');

        $this
            ->icon('heroicon-m-plus-circle')
            ->color('danger')
            ->requiresconfirmation()
            ->modalWidth(MaxWidth::Small)
            ->form([
                Forms\Components\Select::make('cancel_reason')
                    ->options(fn() => $reasons)
                    ->required()
                    ->suffixAction(
                        Action::make('create-cancel-reason')
                            ->icon('heroicon-m-plus-circle')
                            ->form([
                                Forms\Components\TextInput::make('reason')
                                    ->required()
                            ])
                            ->action(function ($data, $state, $set) {
                                $reason = CancelReason::create([
                                    'reason' => $data['reason'],
                                    'tenant_id' => Filament::getTenant()->id,
                                    'user_id' => auth()->id()
                                ]);
                                $set('reason', $reason->id);
                            })
                    ),
                Forms\Components\TextInput::make('cancellation_charge')
                    ->required()
                    ->minValue(0)
                    ->numeric()
                    ->default(0)
            ]);


        $this->action(function ($data, $livewire): void {
            // Cancel reservation
            $livewire->selectedFolio->update([
                'cancel_reason_id' => $data['cancel_reason'],
                'status' => Status::Cancelled
            ]);

            $cancellation_charge_transaction_id = NULL;
            if (filled($data['cancellation_charge']) && $data['cancellation_charge'] > 0) {
                $cancellation_charge_transaction_id = $livewire->selectedFolio->bookingTransactions()->create([
                    'booking_id' => $livewire->booking->id,
                    'transaction_type' => 'Cancellation Charge',
                    'user_id' => auth()->id(),
                    'date' => now(),
                    'rate' => $data['cancellation_charge']
                ]);
            }

            $livewire->selectedFolio->delete();

            $this->deleteBookingTransactions($livewire->selectedFolio->id, $cancellation_charge_transaction_id);

            activity()->performedOn($livewire->selectedFolio)->log('Reservation Canceled by: ' . auth()->user()->name);

            $livewire->booking->refresh();

            //delete if there are any connecting rooms blocked
            $livewire->booking->bookingReservations->where('status', Status::Maintenance)->each(function ($reservation) use (&$livewire) {
                if ($livewire->selectedFolio->room->family_room_id == $reservation->room->family_room_id) {
                    $this->deleteBookingTransactions($reservation->id);
                    $reservation->delete();
                }
            });

            $livewire->booking->refresh();

            if (!$livewire->booking->bookingReservations->count()) {

                $livewire->booking->update([
                    'Cancel_reason_id' => $data['cancel_reason'],
                    'status' => Status::Cancelled

                ]);

                $livewire->booking->delete();
            }

            $livewire->dispatch('refresh-scheduler');

            $livewire->dispatch('refresh-edit-reservation');

            $livewire->dispatch('close-booking-summary-modal', id: 'booking-summary');

            Notification::make()
                ->title('Reservation Canceled successfull!')
                ->success()
                ->send();
        });

        $this->after(function ($livewire, $data) {
            Cache::forget('reservationBalance_' . $livewire->selectedFolio->id);
        });
    }

    protected function deleteBookingTransactions($reservation_id, $cancellation_charge_transaction_id = NULL): void
    {

        $reservation = BookingTransaction::where('booking_reservation_id', $reservation_id);
        if ($cancellation_charge_transaction_id !== NULL) {
            $reservation->where('id', '!=', $cancellation_charge_transaction_id->id);
        }
        $reservation->delete();
    }
}
