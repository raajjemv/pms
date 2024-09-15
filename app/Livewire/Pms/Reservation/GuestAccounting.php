<?php

namespace App\Livewire\Pms\Reservation;

use App\Models\BookingNight;
use Carbon\Carbon;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables;

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
            ->query(BookingNight::query()->where('booking_id', $this->booking->id))
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
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ])
            ->paginated(false)
            ->striped();
    }

    public function render()
    {
        return view('livewire.pms.reservation.guest-accounting');
    }
}
