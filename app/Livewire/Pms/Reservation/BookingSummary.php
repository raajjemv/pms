<?php

namespace App\Livewire\Pms\Reservation;

use App\Models\Booking;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class BookingSummary extends Component
{
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

    #[On('close-reservation-modal')]
    public function closeReservationModal()
    {
        $this->reset(['booking', 'reservation_id']);
    }


    #[Computed]
    public function reservation()
    {
        return $this->booking?->bookingReservations->where('id', $this->reservation_id)->first();
    }

    public function render()
    {
        return view('livewire.pms.reservation.booking-summary', []);
    }
}
