<?php

namespace App\Http\Controllers\Pdf;

use App\Models\Booking;
use App\Enums\PaymentType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReservationInvoice extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($booking_id)
    {
        $booking = Booking::find(decrypt($booking_id));
        $reservation = $booking->bookingReservations->first();
        $paid = $booking->bookingTransactions
            ->where('booking_reservation_id', $reservation->id)
            ->whereIn('transaction_type', PaymentType::getAllValues())
            ->sum('rate');

        $data = [
            'foo' => 'bar'
        ];

        $pdf = \PDF::loadView('pdf.reservation-invoice', compact('booking', 'reservation', 'paid'));


        return $pdf->stream('document.pdf');
    }
}
