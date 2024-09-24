<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case Inquiry = "inquiry";
    case Hold = "hold";
    case Confirmed = "confirmed";
    case Paid = "paid";
    case CheckIn = "check-in";
    case CheckedOut = "checked-out";
    case Cancelled = "cancelled";
    case NoShow = "no-Show";
    case Overstay = "overstay";
    case Pending = "pending";
    case Disputed = "disputed";
    case Archived = "archived";

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
