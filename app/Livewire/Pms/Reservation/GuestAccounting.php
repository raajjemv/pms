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
use App\Models\BookingTransaction;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
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

        $query = $this->booking->bookingTransactions()->withTrashed();
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
                    ->after(function () {
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
                        Forms\Components\DatePicker::make('date'),
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
                    ->after(function () {
                        $this->dispatch('refresh-edit-reservation');
                    }),

                Actions\Action::make('Change History')
                    ->icon('heroicon-m-list-bullet')
                    ->color('gray')
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
