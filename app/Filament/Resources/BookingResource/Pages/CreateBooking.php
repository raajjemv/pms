<?php

namespace App\Filament\Resources\BookingResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BookingResource;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;
        $data['user_id'] = auth()->id();
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
            $booking->bookingNights()->create([
                'rate' => $booking->room->roomType->base_rate,
                'date' => $date
            ]);
        }
    }
}
