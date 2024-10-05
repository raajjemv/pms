<?php

namespace App\Livewire\Pms\Reservation;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class BookingSummary extends Component
{
    public $booking;

    // #[Reactive]
    public $reservationId;

    // public $selectedReservation;

    public function mount()
    {
        // $this->selectedReservation = $this->booking->bookingReservations->where('id', $this->reservationId)->first();
    }

    // public function selectReservation($reservation)
    // {
    //     $this->selectedReservation = $this->booking->bookingReservations->where('id', $reservation)->first();
    // }

    public function render()
    {
        return view('livewire.pms.reservation.booking-summary', [
            'selectedReservation' => $this->booking->bookingReservations->where('id', $this->reservationId)->first()
        ]);
    }
}
