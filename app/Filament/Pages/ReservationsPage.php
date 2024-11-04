<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\Room;
use Filament\Tables;
use App\Enums\Status;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use Illuminate\Support\Collection;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ReservationsPage extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use CachedQueries;

    protected static ?string $navigationLabel = 'Daily Beat';

    protected ?string $heading = 'Daily Beat';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static string $view = 'filament.pages.reservations-page';

    #[Url(keep: true, except: '')]
    public $activeTab = 'todays-reservations';

    #[Computed]
    public function todaysRevervations()
    {
        return BookingReservation::whereDate('created_at', today())
            ->with('booking')
            ->whereIn('status', [Status::Confirmed, Status::Paid, Status::Reserved]);
    }

    #[Computed]
    public function arrivalReservations()
    {
        return BookingReservation::whereDate('from', today())
            ->with('booking')
            ->whereHas('room')
            ->whereIn('status', [Status::Confirmed, Status::Paid, Status::Reserved]);
    }

    #[Computed]
    public function departureReservations()
    {
        return BookingReservation::whereDate('to', today())
            ->with('booking')
            ->whereHas('room')
            ->whereIn('status', [Status::CheckIn, Status::Overstay]);
    }

    #[Computed]
    public function activeReservations()
    {
        return BookingReservation::where('to', '>=', today())
            ->with('booking')
            ->whereHas('room')
            ->whereIn('status', [Status::CheckIn, Status::Overstay]);
    }

    #[On('refresh-reservations')]
    public function reloadComponent() {}

    public function setTableQuery($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('refresh-reservations');
    }
    public function table(Table $table): Table
    {
        $roomTypes = static::roomTypes();

        $query = match ($this->activeTab) {
            'todays-reservations' => $this->todaysRevervations,
            'arrivals' => $this->arrivalReservations,
            'departures' => $this->departureReservations,
            'active-stays' => $this->activeReservations
        };
        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('booking_customer')
                    ->description(fn($record) => $record->booking->booking_number)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('booking_customer', 'like', "%{$search}%")
                            ->orWhereHas('booking', function ($q) use (&$search) {
                                $q->where('booking_number', $search);
                            });
                    }),
                Tables\Columns\TextColumn::make('roomType.name')
                    ->description(fn($record) => $record->room?->room_number),
                Tables\Columns\TextColumn::make('from')
                    ->description(fn($record) => $record->from->format('H:i a'))
                    ->label('Arrival Date')
                    ->date(),
                Tables\Columns\TextColumn::make('to')
                    ->description(fn($record) => $record->to->format('H:i a'))
                    ->label('Departure Date')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->description(fn($record) => $record->created_at->format('H:i a'))
                    ->label('Reservation Date')
                    ->date(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Tables\Actions\Action::make('Assign Room')
                    ->fillForm(fn($record) => [
                        'room_type' => $record->room_type_id,
                        'rate_plan' => $record->rate_plan_id
                    ])
                    ->form(function ($record) use ($roomTypes) {
                        return [
                            Forms\Components\Group::make([
                                Forms\Components\Select::make('room_type')
                                    ->options($roomTypes->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->live(),

                                Forms\Components\Select::make('rate_plan')
                                    ->options(fn($get) => $get('room_type') ? $roomTypes->where('id', $get('room_type'))->first()->ratePlans->pluck('name', 'id') : [])
                                    ->required()
                                    ->searchable()
                                    ->afterStateUpdated(function ($get, $set) use ($roomTypes) {})
                                    ->live(),

                                Forms\Components\Select::make('room')
                                    ->options(function ($get, $set) use ($record): Collection {
                                        $reservations = BookingReservation::query()
                                            ->where('room_type_id', $get('room_type'))
                                            ->where(function ($query) use ($record) {
                                                $query->whereDate('from', '<=', $record->to)
                                                    ->whereDate('to', '>=', $record->from);
                                            })
                                            ->whereNotNull('room_id')
                                            ->get();
                                        return Room::query()
                                            ->where('room_type_id', $get('room_type'))
                                            ->whereNotIn('id', $reservations->pluck('room_id'))
                                            ->pluck('room_number', 'id');
                                    })
                                    ->required()
                                    // ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->live()
                            ])

                        ];
                    })
                    ->action(function ($data, $record) {
                        $record->update([
                            'room_id' => $data['room']
                        ]);
                        $this->dispatch('refresh-reservations');
                        $this->dispatch('refresh-scheduler');
                        Notification::make()
                            ->title('Room Assigned Succssful')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => is_null($record->room_id)),
                Tables\Actions\Action::make('view')
                    ->action(fn($record) => $this->dispatch('booking-summary', booking_id: $record->booking_id, reservation_id: $record->id))
            ])
            ->bulkActions([
                // ...
            ])
            ->defaultSort('created_at', 'ASC');
    }
}
