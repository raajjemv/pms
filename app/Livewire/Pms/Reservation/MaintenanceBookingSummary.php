<?php

namespace App\Livewire\Pms\Reservation;

use App\Enums\Status;
use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Booking;
use Livewire\Component;
use Filament\Forms\Form;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Livewire\Attributes\Computed;
use App\Models\BookingReservation;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use App\Http\Traits\InteractsWithReservationActions;

class MaintenanceBookingSummary extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithReservationActions;

    public $booking;

    public $reservation_id;

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('from')
                    ->disabled()
                    ->native(false)
                    ->format('Y-m-d'),
                Forms\Components\DatePicker::make('to')

                    ->afterStateUpdated(function ($state, $set, $livewire) {
                        $stateDate = Carbon::parse($state);

                        $nights = totolNights($this->selectedFolio->to->format('Y-m-d'), $state);

                        $set('nights', $nights);
                    })
                    ->native(false)
                    ->minDate(fn($livewire) => $this->selectedFolio->to)
                    ->disabledDates(fn($state, $livewire) => roomReservationsByMonth(
                        $this->selectedFolio->room_id,
                        Carbon::parse($state)->startOfMonth(),
                        Carbon::parse($state)->endOfMonth()
                    )->merge($this->selectedFolio->to->format('Y-m-d'))->toArray())
                    ->format('Y-m-d')
                    ->closeOnDateSelection()
                    ->live()
                    ->rules([
                        fn($livewire): Closure => function (string $attribute,  $value, Closure $fail) use ($livewire) {
                            $nights = totolNights($this->selectedFolio->to->format('Y-m-d'), $value);

                            // if ($nights == 0) {
                            //     $fail('Invalid date chosen!');
                            // }
                            $roomReservationsByMonth = roomReservationsByMonth(
                                $this->selectedFolio->room_id,
                                Carbon::parse($this->selectedFolio->to)->startOfMonth(),
                                Carbon::parse($value)->endOfMonth()
                            );

                            for ($i = 0; $i < $nights; $i++) {
                                $date = $this->selectedFolio->to->copy()->addDays($i);
                                if ($roomReservationsByMonth->contains($date->format('Y-m-d'))) {
                                    $fail('Pick another day. Your reservation falls to a booked date');
                                }
                            }
                        },
                    ]),

                Forms\Components\TextInput::make('nights')
                    ->live()
                    ->numeric()
                    ->afterStateUpdated(function ($state, $set, $livewire) {
                        $date = $this->selectedFolio->to->copy()->addDays(intval($state));
                        $set('to', $date->format('Y-m-d'));
                    })
                    ->formatStateUsing(fn($livewire, $get) => totolNights($this->selectedFolio->to->format('Y-m-d'), $get('to'))),

                Forms\Components\TextInput::make('remark')
                    ->required(),
            ])
            ->statePath('data');
    }


    #[On('maintenance-booking-summary')]
    public function loadBookingSummary($booking_id, $reservation_id)
    {
        $booking = Booking::with('bookingReservations.room.roomType')->find($booking_id);

        $this->booking = $booking;

        $this->reservation_id = $reservation_id;

        $this->form->fill([
            'remark' => $booking->booking_customer,
            'from' => $this->selectedFolio->from,
            'to' => $this->selectedFolio->to,
        ]);
        $this->dispatch('open-modal', id: 'maintenance-booking-summary');
    }

    public function saveReservation()
    {
        $to = Carbon::parse($this->form->getState()['to'])->setTimeFromTimeString(tenant()->check_out_time);
        $nights = $this->form->getState()['nights'];

        $this->selectedFolio->booking_customer = $this->form->getState()['remark'];
        $this->selectedFolio->to = $to;
        if ($this->selectedFolio->isDirty('to')) {
            for ($i = 0; $i < intval($nights); $i++) {
                $date = $this->selectedFolio->to->copy()->addDays($i);
                $this->selectedFolio->bookingTransactions()->create([
                    'booking_id' => $this->booking->id,
                    'rate' => 0,
                    'date' => $date,
                    'transaction_type' => 'room_charge',
                    'user_id' => auth()->id(),
                    'maintenance' => true
                ]);
            }
        }
        $this->selectedFolio->save();

        $this->booking->booking_customer = $this->form->getState()['remark'];
        $this->booking->save();
        activity()->performedOn($this->selectedFolio)->log('Maintenance Period Extended');

        $this->dispatch('refresh-scheduler');
        $this->dispatch('close-maintenance-booking-summary-modal', id: 'maintenance-booking-summary');

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    #[On('close-maintenance-booking-summary-modal')]
    public function closeReservationModal()
    {
        $this->reset(['booking', 'reservation_id']);
    }

    public function unblock(): Action
    {
        return Action::make('unblock')
            ->color('gray')
            ->requiresConfirmation()
            ->action(function () {
                $this->booking->bookingReservations()->each(function ($reservation) {
                    $reservation->delete();
                });
                $this->booking->bookingTransactions()->each(function ($transaction) {
                    $transaction->delete();
                });
                $this->booking->delete();
                $this->booking->delete();
            })
            ->after(function () {
                $this->dispatch('refresh-scheduler');
                $this->dispatch('close-maintenance-booking-summary-modal', id: 'maintenance-booking-summary');

                Notification::make()
                    ->title('Room unblocked successfully')
                    ->success()
                    ->send();
            })
            ->disabled(fn() => $this->booking->bookingReservations->where('status', '!=', Status::Maintenance)->count() > 0);
    }


    #[Computed]
    public function selectedFolio()
    {
        return $this->booking?->bookingReservations->where('id', $this->reservation_id)->first();
    }

    public function render()
    {
        return view('livewire.pms.reservation.maintenance-booking-summary');
    }
}
