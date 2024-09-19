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

        for ($i = 0; $i < $nights; $i++) {
            $date = $booking->from->copy()->addDays($i);
            $booking->bookingTransactions()->create([
                'rate' => $booking->room->roomType->base_rate,
                'date' => $date,
                'transaction_type' => 'room_charge',
                'user_id' => auth()->user()->id()
            ]);
        }
    }
}
