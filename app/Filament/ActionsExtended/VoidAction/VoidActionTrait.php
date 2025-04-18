<?php

namespace App\Filament\ActionsExtended\VoidAction;

use App\Enums\Status;
use Filament\Forms;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use App\Models\BookingTransaction;
use App\Models\VoidReason;
use Filament\Facades\Filament;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Filament\Widgets\StatsOverviewWidget\Stat;

trait VoidActionTrait
{
    use CachedQueries;

    public static function getDefaultName(): ?string
    {
        return 'void-reservation';
    }

    protected function setUp(): void
    {

        parent::setUp();

        $reasons = VoidReason::pluck('reason', 'id');

        $this
            ->icon('heroicon-m-plus-circle')
            ->color('danger')
            ->requiresconfirmation()
            ->modalWidth(MaxWidth::Small)
            ->form([
                Forms\Components\Select::make('void_reason')
                    ->options(fn() => $reasons)
                    ->required()
                    ->suffixAction(
                        Action::make('create-void-reason')
                            ->icon('heroicon-m-plus-circle')
                            ->form([
                                Forms\Components\TextInput::make('reason')
                                    ->required()
                            ])
                            ->action(function ($data, $state, $set) {
                                $reason = VoidReason::create([
                                    'reason' => $data['reason'],
                                    'tenant_id' => Filament::getTenant()->id,
                                    'user_id' => auth()->id()
                                ]);
                                $set('void_reason', $reason->id);
                            })
                    )
            ]);


        $this->action(function ($data, $livewire): void {
            // void reservation
            $livewire->selectedFolio->update([
                'void_reason_id' => $data['void_reason'],
                'status' => Status::Void
            ]);

            $livewire->selectedFolio->delete();

            $this->deleteBookingTransactions($livewire->selectedFolio->id);

            activity()->performedOn($livewire->selectedFolio)->log('Reservation voided by: ' . auth()->user()->name);

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
                    'void_reason_id' => $data['void_reason'],
                    'status' => Status::Void

                ]);

                $livewire->booking->delete();
            }

            $livewire->dispatch('refresh-scheduler');

            $livewire->dispatch('refresh-edit-reservation');

            $livewire->dispatch('close-booking-summary-modal', id: 'booking-summary');

            Notification::make()
                ->title('Reservation Voided successfull!')
                ->success()
                ->send();
        });

        $this->after(function ($livewire, $data) {
            Cache::forget('reservationBalance_' . $livewire->selectedFolio->id);
        });
    }

    protected function deleteBookingTransactions($reservation_id): void
    {
        $reservation = BookingTransaction::where('booking_reservation_id', $reservation_id)
            ->delete();
    }
}
