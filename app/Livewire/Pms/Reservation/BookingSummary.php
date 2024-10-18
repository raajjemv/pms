<?php

namespace App\Livewire\Pms\Reservation;

use App\Models\Booking;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Filament\Forms\Contracts\HasForms;
use App\Forms\Components\GroupCheckField;
use App\Http\Traits\InteractsWithCheckInCheckOut;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class BookingSummary extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithCheckInCheckOut;

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
    public function reservation()
    {
        return $this->booking?->bookingReservations->where('id', $this->reservation_id)->first();
    }

    public function bookingSummaryAction($action)
    {
        return match ($action) {
            'check-in' => $this->replaceMountedAction('checkIn'),
            'check-out' => $this->replaceMountedAction('checkOut'),
        };
    }



    public function render()
    {
        return view('livewire.pms.reservation.booking-summary', []);
    }
}
