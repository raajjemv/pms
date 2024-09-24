<?php

namespace App\Filament\Resources\BookingResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BookingResource;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $date = explode(" to ", $data['date']);

        $from_date = Carbon::parse($date[0]);
        $to_date = isset($date[1]) ? Carbon::parse($date[1]) : $from_date;

        $data['from'] = $from_date;
        $data['to'] = $to_date;

        $data['tenant_id'] = Filament::getTenant()->id;
        $data['user_id'] = auth()->id();
        $data = collect($data)->forget('date')->toArray();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function afterCreate()
    {
        $booking = $this->record;
        $nights = $booking->from->diffInDays($booking->to);

        

        $booking_reservation = $booking->bookingReservations()->create([
            'tenant_id' => Filament::getTenant()->id,
            'room_id' => $booking->room_id,
            'adults' => $booking->adults,
            'children' => $booking->children,
            'rate_plan_id' => $booking->rate_plan_id,
            'booking_customer' => $booking->booking_customer,
            'from' => $booking->from,
            'to' => $booking->to,
        ]);


        for ($i = 0; $i < $nights; $i++) {
            $date = $booking->from->copy()->addDays($i);
            $booking->bookingTransactions()->create([
                'booking_reservation_id' => $booking_reservation->id,
                'rate' => $booking->room->roomType->base_rate,
                'date' => $date,
                'transaction_type' => 'room_charge',
                'user_id' => auth()->id()
            ]);
        }
    }
}
