<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel
{
    case Pending = "pending";
    case PartiallyPaid = "partially_paid";
    case FullyPaid = "fully_paid";
    case Refunded = "refunded";
    case Disputed = "disputed";
    case Voided = "voided";
    case Authorized = "authorized";
    case Captured = "captured";
    case Declined = "declined";
    case Chargeback = "chargeback";


    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
