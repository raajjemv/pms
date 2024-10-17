<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case Inquiry = "inquiry";
    case Reserved = "reserved";
    case Hold = "hold";
    case Confirmed = "confirmed";
    case Paid = "paid";
    case CheckIn = "check-in";
    case CheckOut = "check-out";
    case Cancelled = "cancelled";
    case NoShow = "no-show";
    case Overstay = "overstay";
    // case Pending = "pending";
    case Disputed = "disputed";
    case Archived = "archived";

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Inquiry => "bg-zinc-200",
            self::Hold => "bg-zinc-200",
            self::Reserved => "bg-orange-400 text-white",
            self::Confirmed => "bg-green-600 text-white",
            self::Paid => "bg-green-600 text-white",
            self::CheckIn => "bg-blue-600 text-white ",
            self::CheckOut => "bg-gray-400 text-white",
            self::Cancelled => "bg-red-500 text-white",
            self::NoShow => "bg-black text-white ",
            self::Overstay => "bg-red-800 text-white",
            // self::Pending => "bg-green-600 text-white",
            self::Disputed => "bg-zinc-200",
            self::Archived => "bg-zinc-200",
        };
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
