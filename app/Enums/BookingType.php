<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookingType: string implements HasLabel
{
    case Direct = 'direct';
    case OTA = 'ota';
    case GDS = 'gds';
    case Corporate = 'corporate';
    case Complimentary = 'complimentary';
    case Internal = 'internal';
    case Overbooking = 'overbooking';

    public function getLabel(): ?string
    {
        return $this->name;
    }
    public function getIcon(): string
    {
        return match ($this) {
            self::Direct => 'heroicon-s-globe-americas',
            self::OTA => 'heroicon-s-network-wire',
            self::GDS => 'heroicon-s-server',
            self::Corporate => 'heroicon-s-building-office',
            self::Complimentary => 'heroicon-s-gift',
            self::Internal => 'heroicon-s-clipboard-check',
            self::Overbooking => 'heroicon-s-exclamation-circle',
        };
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
