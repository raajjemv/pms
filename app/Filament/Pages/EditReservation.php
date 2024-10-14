<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use Filament\Pages\Page;
use App\Enums\PaymentType;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\BookingReservation;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Widgets\BookingTotalAmount;
use App\Http\Traits\InteractsWithGuestRegistration;

class EditReservation extends Page
{
    use InteractsWithGuestRegistration;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.edit-reservation';

    protected static bool $shouldRegisterNavigation = false;

    #[Url(keep: true)]
    public $activeTab = 'guest-accounting';

    #[Url(keep: true)]
    public $reservation_id;

    public $booking;

    // public  BookingReservation $selectedFolio;

    #[On('refresh-edit-reservation')]
    public function refreshComponent() {}

    protected function getHeaderWidgets(): array
    {
        return [
            BookingTotalAmount::make([
                'total' => $this->booking
                    ->bookingTransactions
                    ->where('booking_reservation_id', $this->reservation_id)
                    ->whereNotIn('transaction_type', PaymentType::getAllValues())
                    ->sum('rate'),
                'paid' => $this->booking
                    ->bookingTransactions
                    ->where('booking_reservation_id', $this->reservation_id)
                    ->whereIn('transaction_type', PaymentType::getAllValues())
                    ->sum('rate')
            ]),
        ];
    }

    #[Computed]
    public function selectedFolio()
    {
        return $this->booking->bookingReservations->where('id', $this->reservation_id)->first();
    }

    public function getHeading(): string | Htmlable
    {
        $customerName = $this->selectedFolio->booking_customer;
        return new HtmlString("{$customerName} <span class='text-lg font-normal text-gray-500'>{$this->booking->booking_number}</span>");
    }

    public function getSubheading(): string | Htmlable
    {
        return new HtmlString(view('components.pms.reservation-summary-banner', ['reservation' => $this->selectedFolio]));
    }

    public function mount()
    {
        $booking = Booking::with(['bookingTransactions', 'customers', 'bookingReservations.room.roomType'])->findOrFail(decrypt(request('record')));

        $this->booking = $booking;
    }
}
