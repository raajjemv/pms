<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasLabel
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Cheque = 'cheque';
    case CreditCard = 'credit_card';
    case CityLedger = 'city_ledger';

    public function getLabel(): ?string
    {

        return match ($this) {
            self::Cash => 'Cash',
            self::BankTransfer => 'Bank Transfer',
            self::Cheque => 'Cheque',
            self::CreditCard => 'Credit Card',
            self::CityLedger => 'City Ledger',
        };
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
