<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookingType: string implements HasLabel
{
    case Agoda = 'agoda';
    case Booking = 'booking.com';
    case Ctrip = 'ctrip';
    case Trip = 'trip.com';
    case Expedia = 'expedia';
    case Direct = 'direct';
    case WalkIn = 'walk_in';

    public function getLabel(): ?string
    {
        return $this->name;
        
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
