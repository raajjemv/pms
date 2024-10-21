<?php

namespace App\Livewire\Pms\Reservation;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use App\Enums\PaymentType;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Tables\Actions;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\DB;
use App\Filament\Pages\ActivityLog;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Contracts\HasForms;
use App\Filament\Pages\EditReservation;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use App\Forms\Components\GroupCheckField;
use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class GuestAccounting extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use CachedQueries;

    public $booking;

    #[Reactive]
    public $selectedFolio;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function table(Table $table): Table
    {

        $query = $this->selectedFolio->bookingTransactions()->withTrashed();
        $businessSources = static::businessSources();
        $folioOperationCharges = static::folioOperationCharges();

        return $table
            ->query(fn() => $query)
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->dateTime('M d, Y l'),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->formatStateUsing(function ($state, $record) {
                        $data = str($state)->title()->replace('_', ' ');
                        if ($record->business_source_id) {
                            $data .= " [{$record->businessSource->name}]";
                        }
                        return $data;
                    }),
                Tables\Columns\TextColumn::make('rate')
                    ->label('Amount')
                    ->formatStateUsing(fn($state) => number_format($state, 2))
                    ->color(fn($record) => !in_array($record->transaction_type, PaymentType::getAllValues()) ?: 'success'),

                Tables\Columns\TextColumn::make('user.name')

            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                Actions\Action::make('Add Payment')
                    ->modalSubmitActionLabel('Add')
                    ->icon('heroicon-m-currency-dollar')
                    ->color('gray')
                    ->fillForm(fn(): array => [
                        'rate' => reservationTotals($this->selectedFolio->id)['balance']
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['booking_id'] = $this->booking->id;
                        $data['user_id'] = auth()->id();
                        $data['booking_reservation_id'] = $this->selectedFolio->id;
                        return $data;
                    })
                    ->modalWidth(MaxWidth::Small)
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->required(),
                        Forms\Components\TextInput::make('rate')
                            ->label('Amount')
                            ->numeric()
                            ->required(),

                        Forms\Components\Select::make('transaction_type')
                            ->options(PaymentType::class)
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('business_source_id')
                            ->label('Business Source')
                            ->options($businessSources->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn($get) => $get('transaction_type') == 'city_ledger')
                            ->required(fn($get) => $get('transaction_type') == 'city_ledger' ? true : false),

                    ])
                    ->action(function ($data) {
                        BookingTransaction::create($data);
                    })
                    ->after(function ($data) {
                        Cache::forget('reservationBalance_' . $this->selectedFolio->id);
                        activity()->performedOn($this->selectedFolio)->log('Payment of ' . $data['rate'] . ' collected by ' . $data['transaction_type']);
                        $this->dispatch('refresh-edit-reservation');
                    }),
                Actions\Action::make('Add Charge')
                    ->icon('heroicon-m-plus-circle')
                    ->color('gray')
                    ->mutateFormDataUsing(function (array $data) use ($folioOperationCharges) {
                        $data['booking_id'] = $this->booking->id;
                        $data['transaction_type'] = $folioOperationCharges->where('id', $data['transaction_type'])->first()->name;
                        $data['user_id'] = auth()->id();
                        $data['booking_reservation_id'] = $this->selectedFolio->id;
                        return $data;
                    })
                    ->modalWidth(MaxWidth::Small)
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->required(),
                        Forms\Components\Select::make('transaction_type')
                            ->label('Charge')
                            ->options($folioOperationCharges->pluck('name', 'id'))
                            ->afterStateUpdated(fn($set, $state) => $set('rate', $folioOperationCharges->where('id', $state)->first()->rate))
                            ->live(),
                        \LaraZeus\Quantity\Components\Quantity::make('quantity')
                            ->dehydrated(false)
                            ->default(1)
                            ->maxValue(10)
                            ->minValue(1)
                            ->afterStateUpdated(fn($get, $set, $state) => $set('rate', $folioOperationCharges->where('id', $get('transaction_type'))->first()->rate * $state))
                            ->visible(fn($get) => $get('transaction_type')),
                        Forms\Components\TextInput::make('rate')
                            ->label('Amount')
                            ->numeric()
                            ->visible(fn($get) => $get('transaction_type')),


                    ])
                    ->action(function ($data) {
                        BookingTransaction::create($data);
                    })
                    ->after(function ($data) {
                        Cache::forget('reservationBalance_' . $this->selectedFolio->id);
                        activity()->performedOn($this->selectedFolio)->log($data['rate'] . ' Charge Added');
                        $this->dispatch('refresh-edit-reservation');
                    }),


                Actions\ActionGroup::make([
                    Actions\Action::make('extendStay')
                        ->modalWidth('sm')
                        ->icon('heroicon-m-pencil-square')
                        ->color('gray')
                        ->fillForm(fn() => [
                            'from' => $this->selectedFolio->from,
                            'to' => $this->selectedFolio->to,
                        ])
                        ->form(function () {
                            return [
                                Forms\Components\DatePicker::make('from')
                                    ->disabled()
                                    ->format('Y-m-d'),
                                Forms\Components\DatePicker::make('to')

                                    ->afterStateUpdated(function ($state, $set, $component) {
                                        $stateDate = Carbon::parse($state);

                                        $nights = totolNights($this->selectedFolio->to->format('Y-m-d'), $state);

                                        $set('nights', $nights);
                                    })
                                    ->native(false)
                                    ->minDate(fn() => $this->selectedFolio->to)
                                    ->disabledDates(fn($state) => roomReservationsByMonth(
                                        $this->selectedFolio->room_id,
                                        Carbon::parse($state)->startOfMonth(),
                                        Carbon::parse($state)->endOfMonth()
                                    )->toArray(), $this->selectedFolio->to->format('Y-m-d'))
                                    ->format('Y-m-d')
                                    ->closeOnDateSelection()
                                    ->live()
                                    ->rules([
                                        fn(): Closure => function (string $attribute,  $value, Closure $fail) {
                                            $nights = totolNights($this->selectedFolio->to->format('Y-m-d'), $value);

                                            if ($nights == 0) {
                                                $fail('Invalid date chosen!');
                                            }
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
                                    ->afterStateUpdated(function ($state, $set) {
                                        $date = $this->selectedFolio->to->copy()->addDays(intval($state));
                                        $set('to', $date->format('Y-m-d'));
                                    })
                                    ->minValue(1)
                                    ->formatStateUsing(fn($state, $get) => totolNights($this->selectedFolio->to->format('Y-m-d'), $get('to'))),

                            ];
                        })
                        ->action(function ($data) {
                            $reservation = BookingReservation::find($this->selectedFolio->id);

                            $to = Carbon::parse($data['to'])->setTimeFromTimeString(tenant()->check_out_time);

                            $from = Carbon::parse($data['to']);

                            for ($i = 0; $i < intval($data['nights']); $i++) {
                                $date = $reservation->to->copy()->addDays($i);
                                $reservation->bookingTransactions()->create([
                                    'booking_id' => $reservation->booking_id,
                                    'rate' => roomTypeRate($reservation->room->room_type_id, $from->format('Y-m-d'), $reservation->rate_plan_id),
                                    'date' => $date,
                                    'transaction_type' => 'room_charge',
                                    'user_id' => auth()->id()
                                ]);
                            }

                            $reservation->update([
                                'to' => $to,
                                'status' => $reservation->status->value == 'overstay' ? 'check-in' : $reservation->status->value,
                            ]);
                        })
                        ->after(function () {
                            $this->dispatch('refresh-scheduler');
                            $this->dispatch('refresh-edit-reservation');
                            Cache::forget('reservationBalance_'.$this->selectedFolio->id);
                            Notification::make()
                                ->title('Reservation extended successfull!')
                                ->success()
                                ->send();
                        })
                        ->visible(fn() => in_array($this->selectedFolio->status->value, ['check-in', 'overstay'])),
                    Actions\Action::make('earlyCheckOut')
                        ->icon('heroicon-m-arrow-right-end-on-rectangle')
                        ->color('gray')
                        ->form(function () {
                            return [
                                GroupCheckField::make('reservations')
                                    ->type('early-check-out')
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

                                $totalNightsLeft = totolNightsByDates(now(), $reservation->to)->map(fn($date) => $date->format('Y-m-d'));


                                $updateTransactions = $reservation->bookingTransactions()
                                    ->whereIn('date', $totalNightsLeft)
                                    ->where('transaction_type', 'room_charge')
                                    ->update([
                                        'transaction_type' => 'early_Checkout_fee'
                                    ]);


                                $reservation->to = now()->setTimeFromTimeString(tenant()->check_out_time);
                                $reservation->check_out = now();
                                $reservation->status = 'check-out';
                                $reservation->save();

                                activity()->performedOn($reservation)->log('Check-Out Processed');
                            });
                        })
                        ->after(function () {
                            $this->dispatch('refresh-scheduler');
                            $this->dispatch('refresh-edit-reservation');

                            Notification::make()
                                ->title('Check-Out Successfull!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn() => $this->selectedFolio->status->value == 'check-in' && $this->selectedFolio->to->gt(now())),
                    Actions\Action::make('Split Reservation')
                        ->icon('heroicon-m-scissors')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function () {
                            $rep_booking = $this->booking->replicate()->fill([
                                'booking_number' => strtoupper(str()->random()),
                                'booking_customer' => $this->selectedFolio->booking_customer
                            ]);

                            $rep_booking->save();

                            DB::table('booking_reservations')->where('id', $this->selectedFolio->id)
                                ->update([
                                    'booking_id' => $rep_booking->id
                                ]);
                            DB::table('booking_transactions')->where('booking_reservation_id', $this->selectedFolio->id)
                                ->update([
                                    'booking_id' => $rep_booking->id
                                ]);
                            DB::table('booking_customer')->where('booking_reservation_id', $this->selectedFolio->id)
                                ->update([
                                    'booking_id' => $rep_booking->id
                                ]);
                            activity()->performedOn($this->selectedFolio)->log('Splitted Reservation');
                            return redirect(EditReservation::getUrl(['record' => encrypt($rep_booking->id), 'reservation_id' => $this->selectedFolio->id]));
                        })
                        ->visible(fn() => $this->booking->bookingReservations->count() > 1),
                    Actions\Action::make('Change History')
                        ->icon('heroicon-m-list-bullet')
                        ->color('gray')
                        ->url(fn() => ActivityLog::getUrl(['reservation_id' => encrypt($this->selectedFolio->id)])),
                ])
                    ->label('More')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->button()
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\Action::make('edit')
                        ->icon('heroicon-m-pencil-square')
                        ->fillForm(fn($record): array => [
                            'transaction_type' => $record->transaction_type,
                            'date' => $record->date,
                            'rate' => $record->rate
                        ])
                        ->form([
                            Forms\Components\DatePicker::make('date')
                                ->disabled(condition: fn($record) => $record->transaction_type == 'room_charge' ? true : false),
                            Forms\Components\TextInput::make('rate')
                                ->numeric(),
                            Forms\Components\TextInput::make('transaction_type')
                                ->disabled(),
                        ])
                        ->action(function ($record, $data) {
                            $record->update([
                                'rate' => $data['rate']
                            ]);
                        })
                        ->after(function () {
                            Cache::forget('reservationBalance_' . $this->selectedFolio->id);
                            $this->dispatch('refresh-edit-reservation');
                        })
                        ->modalWidth(MaxWidth::Small)
                        ->slideOver(),
                    Actions\Action::make('send')
                        ->icon('heroicon-m-paper-airplane')
                        ->visible(fn($record) => $record->transaction_type !== 'room_charge'),
                    Actions\DeleteAction::make()
                        ->label('Void')
                        ->after(function () {
                            $this->dispatch('refresh-edit-reservation');
                            Cache::forget('reservationBalance_' . $this->selectedFolio->id);
                        })
                        ->modalHeading('Delete record?')
                        ->visible(fn($record) => $record->transaction_type !== 'room_charge'),


                ])
                    ->visible(fn($record) => !$record->trashed())
                    ->iconButton()
            ])
            ->bulkActions([
                // ...
            ])
            ->paginated(false)
            ->striped()
            ->defaultSort('date', 'ASC')
            ->recordClasses(fn($record) => $record->trashed() ? '!opacity-50 line-through !py-1' : '!py-1');
    }

    public function render()
    {
        return view('livewire.pms.reservation.guest-accounting');
    }
}
