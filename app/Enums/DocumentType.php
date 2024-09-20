<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasLabel
{
    case IDCard = 'id_card';
    case Passport = 'passport';
    case DrivingLicense = 'driving_license';

    public function getLabel(): ?string
    {

        return match ($this) {
            self::IDCard => 'ID Card',
            self::Passport => 'Passport',
            self::DrivingLicense => 'Driving License',
        };
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
