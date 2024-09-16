<?php

namespace App\Livewire\Pms\Reservation;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\BookingNight;
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

    public $booking;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(BookingNight::query()->where('booking_id', $this->booking->id)->withTrashed())
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->dateTime('M d, Y l'),
                Tables\Columns\TextColumn::make('charge_type')
                    ->formatStateUsing(fn($state) => str($state)->replace('_', ' ')->title()),
                Tables\Columns\TextColumn::make('rate')
                    ->label('Amount')
                    ->formatStateUsing(fn($state) => number_format($state, 2))
                    ->color(fn($record) => !in_array($record->charge_type, ['credit_card', 'cash', 'bank_transfer']) ?: 'success'),

            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->icon('heroicon-m-currency-dollar')
                    ->color('gray')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['booking_id'] = $this->booking->id;

                        return $data;
                    })
                    ->form([
                        Forms\Components\DatePicker::make('date'),
                        Forms\Components\TextInput::make('rate')
                            ->numeric(),
                        Forms\Components\Select::make('charge_type')
                            ->options(['credit_card', 'cash', 'bank_transfer']),
                    ]),
                Actions\Action::make('Add Charge')
                    ->icon('heroicon-m-plus-circle')
                    ->color('gray'),
                Actions\Action::make('Change History')
                    ->icon('heroicon-m-list-bullet')
                    ->color('gray')
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\Action::make('edit')
                        ->icon('heroicon-m-pencil-square')
                        ->fillForm(fn($record): array => [
                            'charge_type' => $record->charge_type,
                            'date' => $record->date,
                            'rate' => $record->rate
                        ])
                        ->form([
                            Forms\Components\DatePicker::make('date')
                                ->disabled(condition: fn($record) => $record->charge_type == 'room_charge' ? true : false),
                            Forms\Components\TextInput::make('rate')
                                ->numeric(),
                            Forms\Components\TextInput::make('charge_type')
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
                        ->visible(fn($record) => $record->charge_type !== 'room_charge'),
                    Actions\DeleteAction::make()
                        ->label('Void')
                        ->modalHeading('Delete record?')
                        ->visible(fn($record) => $record->charge_type !== 'room_charge'),


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
