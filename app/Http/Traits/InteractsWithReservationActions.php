<?php

namespace App\Http\Traits;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Enums\PaymentType;
use Filament\Actions\Action;
use App\Models\BookingReservation;
use App\Models\BookingTransaction;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use App\Forms\Components\GroupCheckField;
use Filament\Tables\Actions\Action as TableAction;

trait InteractsWithReservationActions
{
    use CachedQueries;
    public function checkInAction(): Action
    {
        return Action::make('checkInAction')
            ->icon('heroicon-m-check-circle')
            ->color('gray')
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
                    clearSchedulerCache($reservation->from, $reservation->to);
                });
                $this->dispatch('refresh-scheduler');
                Notification::make()
                    ->title('Check-In Successfull!')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }
    public function checkOutAction(): Action
    {
        return Action::make('checkOutAction')
            ->icon('heroicon-m-check-circle')
            ->color('gray')
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
                    clearSchedulerCache($reservation->from, $reservation->to);

                });
                $this->dispatch('refresh-scheduler');
                Notification::make()
                    ->title('Check-Out Successfull!')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }

//     public function extendStayTableAction()
//     {
//         return TableAction::make('extendStay')
//             ->modalWidth('sm')
//             ->icon('heroicon-m-pencil-square')
//             ->color('gray')
//             ->fillForm(fn() => [
//                 'from' => $this->selectedFolio->from,
//                 'to' => $this->selectedFolio->to,
//             ])
//             ->form(function () {
//                 return [
//                     Forms\Components\DatePicker::make('from')
//                         ->disabled()
//                         ->format('Y-m-d'),
//                     Forms\Components\DatePicker::make('to')
// 
//                         ->afterStateUpdated(function ($state, $set, $component) {
//                             $stateDate = Carbon::parse($state);
// 
//                             $nights = totolNights($this->selectedFolio->to->format('Y-m-d'), $state);
// 
//                             $set('nights', $nights);
//                         })
//                         ->native(false)
//                         ->minDate(fn() => $this->selectedFolio->to)
//                         ->disabledDates(fn($state) => roomReservationsByMonth(
//                             $this->selectedFolio->room_id,
//                             Carbon::parse($state)->startOfMonth(),
//                             Carbon::parse($state)->endOfMonth()
//                         )->toArray(), $this->selectedFolio->to->format('Y-m-d'))
//                         ->format('Y-m-d')
//                         ->closeOnDateSelection()
//                         ->live()
//                         ->rules([
//                             fn(): Closure => function (string $attribute,  $value, Closure $fail) {
//                                 $nights = totolNights($this->selectedFolio->to->format('Y-m-d'), $value);
// 
//                                 if ($nights == 0) {
//                                     $fail('Invalid date chosen!');
//                                 }
//                                 $roomReservationsByMonth = roomReservationsByMonth(
//                                     $this->selectedFolio->room_id,
//                                     Carbon::parse($this->selectedFolio->to)->startOfMonth(),
//                                     Carbon::parse($value)->endOfMonth()
//                                 );
// 
//                                 for ($i = 0; $i < $nights; $i++) {
//                                     $date = $this->selectedFolio->to->copy()->addDays($i);
//                                     if ($roomReservationsByMonth->contains($date->format('Y-m-d'))) {
//                                         $fail('Pick another day. Your reservation falls to a booked date');
//                                     }
//                                 }
//                             },
//                         ]),
//                     Forms\Components\TextInput::make('nights')
//                         ->live()
//                         ->numeric()
//                         ->afterStateUpdated(function ($state, $set) {
//                             $date = $this->selectedFolio->to->copy()->addDays(intval($state));
//                             $set('to', $date->format('Y-m-d'));
//                         })
//                         ->minValue(1)
//                         ->formatStateUsing(fn($state, $get) => totolNights($this->selectedFolio->to->format('Y-m-d'), $get('to'))),
// 
//                 ];
//             })
//             ->action(function ($data) {
//                 $reservation = BookingReservation::find($this->selectedFolio->id);
// 
//                 $to = Carbon::parse($data['to'])->setTimeFromTimeString(tenant()->check_out_time);
// 
//                 $from = Carbon::parse($data['to']);
// 
//                 for ($i = 0; $i < intval($data['nights']); $i++) {
//                     $date = $reservation->to->copy()->addDays($i);
//                     $reservation->bookingTransactions()->create([
//                         'booking_id' => $reservation->booking_id,
//                         'rate' => roomTypeRate($reservation->room->room_type_id, $from->format('Y-m-d'), $reservation->rate_plan_id),
//                         'date' => $date,
//                         'transaction_type' => 'room_charge',
//                         'user_id' => auth()->id()
//                     ]);
//                 }
// 
//                 $reservation->update([
//                     'to' => $to,
//                     'status' => $reservation->status->value == 'overstay' ? 'check-in' : $reservation->status->value,
//                 ]);
//             })
//             ->after(function () {
//                 $this->dispatch('refresh-scheduler');
//                 $this->dispatch('refresh-edit-reservation');
//                 Cache::forget('reservationBalance_' . $this->selectedFolio->id);
//                 Notification::make()
//                     ->title('Reservation extended successfull!')
//                     ->success()
//                     ->send();
//             })
//             ->visible(fn() => in_array($this->selectedFolio->status->value, ['check-in', 'overstay']));
//     }
    //     public function earlyCheckOutTableAction()
    //     {
    //         return TableAction::make('earlyCheckOut')
    //             ->icon('heroicon-m-arrow-right-end-on-rectangle')
    //             ->color('gray')
    //             ->form(function () {
    //                 return [
    //                     GroupCheckField::make('reservations')
    //                         ->type('early-check-out')
    //                         ->options(fn() => $this->booking->bookingReservations->pluck('booking_customer', 'id'))
    //                         ->required()
    //                         ->validationMessages([
    //                             'required' => 'Select a reservation to proceed!',
    //                         ])
    //                 ];
    //             })
    //             ->action(function ($data) {
    //                 collect($data['reservations'])->each(function ($reservation_id) {
    //                     $reservation = BookingReservation::find($reservation_id);
    // 
    //                     $totalNightsLeft = totolNightsByDates(now(), $reservation->to)->map(fn($date) => $date->format('Y-m-d'));
    // 
    // 
    //                     $updateTransactions = $reservation->bookingTransactions()
    //                         ->whereIn('date', $totalNightsLeft)
    //                         ->where('transaction_type', 'room_charge')
    //                         ->update([
    //                             'transaction_type' => 'early_Checkout_fee'
    //                         ]);
    // 
    // 
    //                     $reservation->to = now()->setTimeFromTimeString(tenant()->check_out_time);
    //                     $reservation->check_out = now();
    //                     $reservation->status = 'check-out';
    //                     $reservation->save();
    // 
    //                     activity()->performedOn($reservation)->log('Check-Out Processed');
    //                 });
    //             })
    //             ->after(function () {
    //                 $this->dispatch('refresh-scheduler');
    //                 $this->dispatch('refresh-edit-reservation');
    // 
    //                 Notification::make()
    //                     ->title('Check-Out Successfull!')
    //                     ->success()
    //                     ->send();
    //             })
    //             ->requiresConfirmation()
    //             ->visible(fn() => $this->selectedFolio->status->value == 'check-in' && $this->selectedFolio->to->gt(now()));
    //     }
}
