<?php

namespace App\Livewire\Pms\Reservation;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Enums\Status;
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
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Http\Traits\InteractsWithReservationActions;
use App\Filament\ActionsExtended\ChargeAction\ChargeTableAction;
use App\Filament\ActionsExtended\PaymentAction\PaymentTableAction;
use App\Filament\ActionsExtended\MoveRoomAction\MoveRoomTableAction;
use App\Filament\ActionsExtended\ExtendStayAction\ExtendStayTableAction;
use App\Filament\ActionsExtended\EarlyCheckOutAction\EarlyCheckOutTableAction;

class GuestAccounting extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use CachedQueries;
    use InteractsWithReservationActions;

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
                PaymentTableAction::make(),
           
                ChargeTableAction::make(),

                Actions\ActionGroup::make([
                    MoveRoomTableAction::make(),

                    ExtendStayTableAction::make(),

                    EarlyCheckOutTableAction::make(),

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
                    ->visible(fn() => !in_array($this->selectedFolio->status, [Status::Archived, Status::Cancelled, Status::Void, Status::Disputed, Status::NoShow]))

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
