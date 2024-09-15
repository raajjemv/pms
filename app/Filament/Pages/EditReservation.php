<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BookingTotalAmount;
use App\Models\Booking;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class EditReservation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.edit-reservation';

    protected function getHeaderWidgets(): array
    {
        return [
            BookingTotalAmount::make([
                'total' => $this->booking
                    ->bookingNights
                    ->where('charge_type', 'room_charge')
                    ->sum('rate'),
                'paid' => $this->booking
                    ->bookingNights
                    ->whereIn('charge_type', ['credit_card', 'cash', 'bank_transfer'])
                    ->sum('rate')
            ]),
        ];
    }

    public $activeTab = 'guest-accounting';

    public $booking;

    public function getHeading(): string | Htmlable
    {
        $customerName = $this->booking->customer_id ? $this->booking->customer->name : $this->booking->booking_customer;
        // return "{$customerName}";
        return new HtmlString("{$customerName} <span class='text-lg font-normal text-gray-500'>{$this->booking->booking_number}</span>");
    }

    public function getSubheading(): string | Htmlable | null
    {
        return "[{$this->booking->room->roomType->name}-{$this->booking->room->room_number}]";
    }
    public function mount()
    {
        $booking = Booking::with(['customer', 'room.roomType', 'bookingNights'])->findOrFail(decrypt(request('record')));
        return $this->booking = $booking;
    }
}
