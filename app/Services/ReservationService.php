<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Booking;
use Filament\Facades\Filament;
use App\Services\BookingService;
use App\Models\BookingReservation;

class ReservationService
{

    public function create(Booking $booking, $data): BookingReservation
    {
        $from = Carbon::parse($data['from'])->setTimeFromTimeString(tenant()->check_in_time);

        $to = Carbon::parse($data['to'])->setTimeFromTimeString(tenant()->check_out_time);

        $booking_reservation = $booking->bookingReservations()->create([
            'tenant_id' => Filament::getTenant()->id,
            'room_id' => $data['room'],
            'adults' => $data['adults'] ?? 0,
            'children' => $data['children'] ?? 0,
            'rate_plan_id' => $data['rate_plan'] ?? NULL,
            'booking_customer' => $data['guest_name'],
            'from' => $from,
            'to' => $to,
            'master' => $data['master'] ?? true,
            'status' => $data['status']
        ]);

        $nights = $booking_reservation->from->diffInDays($booking_reservation->to);

        for ($i = 0; $i < $nights; $i++) {
            $date = $booking_reservation->from->copy()->addDays($i);
            $booking->bookingTransactions()->create([
                'booking_reservation_id' => $booking_reservation->id,
                'rate' => in_array($data['status'], ['maintenance']) ? 0 : roomTypeRate($booking_reservation->room->room_type_id, $from->format('Y-m-d'), $data['rate_plan']),
                'date' => $date,
                'transaction_type' => 'room_charge',
                'user_id' => auth()->id(),
                'maintenance' => $data['status'] == 'maintenance' ? true : false
            ]);
        }

        clearSchedulerCache($booking_reservation->from, $booking_reservation->to);

        return $booking_reservation;
    }

    public function blockConnectingRooms(Booking $booking, BookingReservation $booking_reservation)
    {
        $bookingRoom = Room::find($booking_reservation->room_id);


        if ($bookingRoom->family_room) {
            $connectingRooms = Room::where('family_room_id', $bookingRoom->family_room_id)
                ->where('id', '!=', $bookingRoom->id)
                ->get();
        } else {
            $connectingRooms = Room::where('family_room_id', $bookingRoom->family_room_id)
                ->where('id', '!=', $bookingRoom->id)
                ->where('family_room', true)
                ->get();
        }


        if ($connectingRooms->count() > 0) {
            foreach ($connectingRooms as $key => $cr) {

                $data['booking_type'] = 'maintenance';
                $data['guest_name'] = $bookingRoom->roomType->name . ': ' . $bookingRoom->room_number;
                $data['status'] = 'maintenance';

                $data['from'] = $booking_reservation->from;
                $data['to'] = $booking_reservation->to;
                $data['room'] = $cr->id;

                $reservation = $this->create($booking, $data);
            }
        }
    }
}
