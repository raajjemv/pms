<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Booking;
use Filament\Facades\Filament;

class BookingService
{

    public function create($data): Booking
    {
        return Booking::create([
            'booking_type' => $data['booking_type'],
            'booking_type_reference' => $data['booking_type_reference'] ?? NULL,
            'business_source_id' => $data['business_source'] ?? NULL,
            'tenant_id' => Filament::getTenant()->id,
            'booking_number' =>  strtoupper(str()->random()),
            'booking_customer' => $data['guest_name'],
            'booking_email' => $data['email'] ?? NULL,
            'user_id' => auth()->id(),
            'status' => $data['status'] ?? NULL
        ]);
    }
}
