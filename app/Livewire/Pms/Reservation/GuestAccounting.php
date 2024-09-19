<?php

namespace App\Livewire\Pms\Reservation;

use App\Enums\PaymentType;
use App\Http\Traits\CachedQueries;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\BookingTransaction;
use Filament\Tables\Actions;
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

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function table(Table $table): Table
    {
        $query = BookingTransaction::query()->where('booking_id', $this->booking->id)->withTrashed();
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

            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Add Payment')
                    ->modalSubmitActionLabel('Add')
                    ->icon('heroicon-m-currency-dollar')
                    ->color('gray')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['booking_id'] = $this->booking->id;
                        return $data;
                    })
                    ->modalWidth(MaxWidth::Small)
                    ->form([
                        Forms\Components\DatePicker::make('date'),
                        Forms\Components\TextInput::make('rate')
                            ->label('Amount')
                            ->numeric(),
                        Forms\Components\Select::make('transaction_type')
                            ->options(PaymentType::class)
                            ->live(),
                        Forms\Components\Select::make('business_source_id')
                            ->label('Business Source')
                            ->options($businessSources->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn($get) => $get('transaction_type') == 'city_ledger'),
                    ])
                    ->createAnother(false),
                Actions\CreateAction::make()
                    ->label('Add Charge')
                    ->icon('heroicon-m-plus-circle')
                    ->color('gray')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['booking_id'] = $this->booking->id;
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
                        Forms\Components\TextInput::make('quantity')
                            ->dehydrated(true)
                            ->numeric(),
                        Forms\Components\TextInput::make('rate')
                            ->label('Amount')
                            ->numeric(),

                    ])
                    ->createAnother(false),

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
                            $this->dispatch('refresh-edit-reservation');
                        })
                        ->modalWidth(MaxWidth::Small)
                        ->slideOver(),
                    Actions\Action::make('send')
                        ->icon('heroicon-m-paper-airplane')
                        ->visible(fn($record) => $record->transaction_type !== 'room_charge'),
                    Actions\DeleteAction::make()
                        ->label('Void')
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
            ->recordClasses(fn($record) => $record->trashed() ? '!opacity-50 line-through !py-1' : '!py-1');
    }

    public function render()
    {
        return view('livewire.pms.reservation.guest-accounting');
    }
}
