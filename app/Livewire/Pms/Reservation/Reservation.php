<?php

namespace App\Livewire\Pms\Reservation;

use App\Enums\Status;
use App\Models\Booking;
use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use App\Models\BookingReservation;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Forms\Components\GroupCheckField;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Http\Traits\InteractsWithGuestRegistration;
use Filament\Actions\Concerns\InteractsWithActions;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Locked;

#[Isolate]
class Reservation extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithGuestRegistration;

    public $booking_id;

    public $reservation_id;

    #[Url(except: '')]
    public $activeTab = '';

    #[On('refresh-edit-reservation')]
    public function refreshComponent() {}


    #[On('open-reservation')]
    public function loadReservationDetails($booking_id, $reservation_id)
    {
        $this->activeTab = 'guest-accounting';
        unset($this->booking);
        unset($this->reservation);


        $this->booking_id = $booking_id;

        $this->reservation_id = $reservation_id;

        $this->dispatch('open-modal', id: 'reservation-modal');
    }

    #[Computed(persist: true)]
    public function booking()
    {
        if ($this->booking_id) {
            return Booking::with(['bookingReservations' => function ($q) {
                $q->with('room.roomType')->withTrashed();
            }, 'bookingReservations' => function ($q) {
                $q->with('ratePlan')->withTrashed();
            }])
                ->withTrashed()
                ->find($this->booking_id);
        }
    }

    #[Computed(persist: true)]
    public function reservation()
    {
        return $this->booking?->bookingReservations->where('id', $this->reservation_id)->first();
    }

    #[On('close-reservation-modal')]
    public function closeReservationModal()
    {
        $this->reset(['reservation_id', 'activeTab']);
        $this->dispatch('refresh-scheduler');
        unset($this->booking);
        unset($this->reservation);
    }


    public function checkInAction(): Action
    {
        return Action::make('checkIn')
            ->icon('heroicon-m-check-circle')
            ->form(function () {
                return [
                    GroupCheckField::make('reservations')
                        ->type('check-in')
                        ->options(fn() => $this->booking->bookingReservations->where('status', '!=', Status::Maintenance)->pluck('booking_customer', 'id'))
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
                $this->dispatch('refresh-scheduler');
                unset($this->reservation);

                Notification::make()
                    ->title('Check-In Successfull!')
                    ->success()
                    ->send();
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
                        ->options(fn() => $this->booking->bookingReservations->where('status', '!=', Status::Maintenance)->pluck('booking_customer', 'id'))
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
                $this->dispatch('refresh-scheduler');
                unset($this->reservation);

                Notification::make()
                    ->title('Check-Out Successfull!')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }

    public function switchReservation($reservation_id)
    {
        // 
        $this->reservation_id = $reservation_id;
        unset($this->reservation);
    }

    public function render()
    {
        return view('livewire.pms.reservation.reservation');
    }
}
