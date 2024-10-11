<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use Filament\Pages\Page;
use App\Enums\PaymentType;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
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

    public $booking;

    public  BookingReservation $selectedFolio;

    #[On('refresh-the-component')]
    public function refreshComponent() {}

    protected function getHeaderWidgets(): array
    {
        return [
            BookingTotalAmount::make([
                'total' => $this->booking
                    ->bookingTransactions
                    ->where('booking_reservation_id', $this->selectedFolio->id)
                    ->whereNotIn('transaction_type', PaymentType::getAllValues())
                    ->sum('rate'),
                'paid' => $this->booking
                    ->bookingTransactions
                    ->where('booking_reservation_id', $this->selectedFolio->id)
                    ->whereIn('transaction_type', PaymentType::getAllValues())
                    ->sum('rate')
            ]),
        ];
    }

    public function setSelectedFolio(BookingReservation $selectedFolio)
    {
        $this->selectedFolio = $selectedFolio;
    }

    public function getHeading(): string | Htmlable
    {
        $customerName = $this->selectedFolio->booking_customer;
        return new HtmlString("{$customerName} <span class='text-lg font-normal text-gray-500'>{$this->booking->booking_number}</span>");
    }


    public function mount()
    {
        $booking = Booking::with(['customer', 'bookingTransactions', 'customers', 'bookingReservations.room'])->findOrFail(decrypt(request('record')));

        $this->selectedFolio =  $booking->bookingReservations->where('master', true)->first();

        return $this->booking = $booking;
    }
}
