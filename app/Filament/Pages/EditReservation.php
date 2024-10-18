<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use Filament\Pages\Page;
use App\Enums\PaymentType;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\BookingReservation;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Widgets\BookingTotalAmount;
use App\Forms\Components\GroupCheckField;
use App\Http\Traits\InteractsWithGuestRegistration;
use Filament\Forms;

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

    #[On('refresh-edit-reservation')]
    public function refreshComponent() {}

    protected function getHeaderWidgets(): array
    {
        return [
            BookingTotalAmount::make([
                'total' => reservationTotals($this->reservation_id)['total'],
                'paid' => reservationTotals($this->reservation_id)['paid']
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
        // return new HtmlString("<a href='{' class='pr-4'><i class='fa-solid fa-arrow-left-long'></i></a> {$customerName} <span class='text-lg font-normal text-gray-500'>{$this->booking->booking_number}</span>");
        return new HtmlString(view('components.pms.reservation-summary-header', ['reservation' => $this->selectedFolio,'booking_number' =>$this->booking->booking_number ]));
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


    public function checkInAction(): Action
    {
        return Action::make('checkIn')
            ->icon('heroicon-m-check-circle')
            ->form(function () {
                return [
                    GroupCheckField::make('reservations')
                        ->type('check-in')
                        ->options(fn() => $this->booking->bookingReservations->pluck('booking_customer', 'id'))
                        ->required()
                        ->validationMessages([
                            'required' => 'Select a reservation to proceed!',
                        ])
                ];
            })
            ->action(function ($data) {
                collect($data['reservations'])->each(function ($reservation_id) {
                    $reservation = BookingReservation::find($reservation_id);
                    $reservation->check_in = now();
                    $reservation->status = 'check-in';
                    $reservation->save();

                    activity()->performedOn($reservation)->log('Check-In Processed');
                });
            })
            ->requiresConfirmation();
    }
    public function checkOutAction(): Action
    {
        return Action::make('checkOut')
            ->icon('heroicon-m-arrow-right-start-on-rectangle')
            ->color('danger')
            ->form(function () {
                return [
                    GroupCheckField::make('reservations')
                        ->type('check-out')
                        ->options(fn() => $this->booking->bookingReservations->pluck('booking_customer', 'id'))
                        ->required()
                        ->validationMessages([
                            'required' => 'Select a reservation to proceed!',
                        ])
                ];
            })
            ->action(function ($data) {
                collect($data['reservations'])->each(function ($reservation_id) {
                    $reservation = BookingReservation::find($reservation_id);
                    $reservation->check_out = now();
                    $reservation->status = 'check-out';
                    $reservation->save();

                    activity()->performedOn($reservation)->log('Check-Out Processed');
                });
            })
            ->requiresConfirmation();
    }
}
