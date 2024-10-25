<?php

namespace App\Livewire\Pms\Reservation;

use App\Filament\ActionsExtended\ChargeAction\ChargeAction;
use App\Filament\ActionsExtended\MoveRoomAction\MoveRoomAction;
use App\Filament\ActionsExtended\PaymentAction\PaymentAction;
use App\Models\Booking;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Filament\Forms\Contracts\HasForms;
use App\Forms\Components\GroupCheckField;
use App\Http\Traits\InteractsWithReservationActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class BookingSummary extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithReservationActions;

    public $booking;

    public $reservation_id;

    #[On('booking-summary')]
    public function loadBookingSummary($booking_id, $reservation_id)
    {
        $booking = Booking::with('bookingReservations.room.roomType')->find($booking_id);

        $this->booking = $booking;

        $this->reservation_id = $reservation_id;

        $this->dispatch('open-modal', id: 'booking-summary');
    }

    #[On('close-booking-summary-modal')]
    public function closeReservationModal()
    {
        $this->reset(['booking', 'reservation_id']);
    }


    #[Computed]
    public function selectedFolio()
    {
        return $this->booking?->bookingReservations->where('id', $this->reservation_id)->first();
    }

    public function bookingSummaryAction($action)
    {
        return match ($action) {
            'check-in' => $this->replaceMountedAction('checkInAction'),
            'check-out' => $this->replaceMountedAction('checkOutAction'),
            'add-payment' => $this->replaceMountedAction('addPaymentAction'),
            'add-charge' => $this->replaceMountedAction('addChargeAction'),
            'move-room' => $this->replaceMountedAction('moveRoomAction')
        };
    }

    public function addPaymentAction()
    {
        return PaymentAction::make('add-payment');
    }

    public function moveRoomAction()
    {
        return MoveRoomAction::make('move-room');
    }

    public function addChargeAction()
    {
        return ChargeAction::make('add-charge');
    }

    public function render()
    {
        return view('livewire.pms.reservation.booking-summary', []);
    }
}
