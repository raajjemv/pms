<?php

namespace App\Filament\ActionsExtended\ExtendStayAction;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;

trait ExtendStayActionTrait
{
    use CachedQueries;

    public static function getDefaultName(): ?string
    {
        return 'extend-stay';
    }

    protected function setUp(): void
    {

        parent::setUp();

        $this
            ->modalWidth('sm')
            ->icon('heroicon-m-pencil-square')
            ->color('gray')
            ->fillForm(fn($livewire) => [
                'from' => $livewire->selectedFolio->from,
                'to' => $livewire->selectedFolio->to,
            ])
            ->form(function () {
                return [
                    Forms\Components\DatePicker::make('from')
                        ->disabled()
                        ->format('Y-m-d'),
                    Forms\Components\DatePicker::make('to')

                        ->afterStateUpdated(function ($state, $set, $livewire) {
                            $stateDate = Carbon::parse($state);

                            $nights = totolNights($livewire->selectedFolio->to->format('Y-m-d'), $state);

                            $set('nights', $nights);
                        })
                        ->native(false)
                        ->minDate(fn($livewire) => $livewire->selectedFolio->to)
                        ->disabledDates(fn($state, $livewire) => roomReservationsByMonth(
                            $livewire->selectedFolio->room_id,
                            Carbon::parse($state)->startOfMonth(),
                            Carbon::parse($state)->endOfMonth()
                        )->merge($livewire->selectedFolio->to->format('Y-m-d'))->toArray())
                        ->format('Y-m-d')
                        ->closeOnDateSelection()
                        ->live()
                        ->rules([
                            fn($livewire): Closure => function (string $attribute,  $value, Closure $fail) use ($livewire) {
                                $nights = totolNights($livewire->selectedFolio->to->format('Y-m-d'), $value);

                                if ($nights == 0) {
                                    $fail('Invalid date chosen!');
                                }
                                $roomReservationsByMonth = roomReservationsByMonth(
                                    $livewire->selectedFolio->room_id,
                                    Carbon::parse($livewire->selectedFolio->to)->startOfMonth(),
                                    Carbon::parse($value)->endOfMonth()
                                );

                                for ($i = 0; $i < $nights; $i++) {
                                    $date = $livewire->selectedFolio->to->copy()->addDays($i);
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
                            $date = $livewire->selectedFolio->to->copy()->addDays(intval($state));
                            $set('to', $date->format('Y-m-d'));

                            $reservation = BookingReservation::find($livewire->selectedFolio->id);
                            $rate = roomTypeRate($reservation->room->room_type_id, $livewire->selectedFolio->to->format('Y-m-d'), $reservation->rate_plan_id);
                            $set('rate', $rate);
                        })
                        ->minValue(1)
                        ->formatStateUsing(fn($livewire, $get) => totolNights($livewire->selectedFolio->to->format('Y-m-d'), $get('to'))),

                    Forms\Components\TextInput::make('rate')
                        ->numeric()
                        ->required(),

                ];
            })
            ->visible(fn($livewire) => in_array($livewire->selectedFolio->status->value, ['check-in', 'overstay']));


        $this->action(function ($data, $livewire): void {
            $reservation = BookingReservation::find($livewire->selectedFolio->id);

            $to = Carbon::parse($data['to'])->setTimeFromTimeString(tenant()->check_out_time);

            $from = Carbon::parse($data['to']);

            for ($i = 0; $i < intval($data['nights']); $i++) {
                $date = $reservation->to->copy()->addDays($i);
                $reservation->bookingTransactions()->create([
                    'booking_id' => $reservation->booking_id,
                    'rate' => $data['rate'],
                    'date' => $date,
                    'transaction_type' => 'room_charge',
                    'user_id' => auth()->id()
                ]);
            }

            $reservation->update([
                'to' => $to,
                'status' => $reservation->status->value == 'overstay' ? 'check-in' : $reservation->status->value,
            ]);
        });

        $this->after(function ($livewire, $data) {
            $livewire->dispatch('refresh-scheduler');
            $livewire->dispatch('refresh-edit-reservation');
            Cache::forget('reservationBalance_' . $livewire->selectedFolio->id);
            activity()->performedOn($livewire->selectedFolio)->log('Reservation Extended');
            Notification::make()
                ->title('Reservation extended successfull!')
                ->success()
                ->send();
        });
    }
}
