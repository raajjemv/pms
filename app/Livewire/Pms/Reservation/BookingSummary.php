<?php

namespace App\Livewire\Pms\Reservation;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class BookingSummary extends Component
{
    public $booking;

    public $reservationId;

    public function render()
    {
        return view('livewire.pms.reservation.booking-summary', [
            'selectedReservation' => $this->booking->bookingReservations->where('id', $this->reservationId)->first()
        ]);
    }
}
