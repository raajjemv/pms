<?php

namespace App\Livewire\Pms\Reservation;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Room;
use Livewire\Component;
use Filament\Forms\Form;
use Livewire\Attributes\On;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use Illuminate\Support\Collection;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class BulkRoomAssign extends Component implements HasForms
{
    use InteractsWithForms;
    use CachedQueries;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public $reservations = [];

    #[On('bulk-room-assign')]
    public function loadReservationDetails($ids = [])
    {
        $this->reservations = BookingReservation::find($ids);
        $this->dispatch('open-modal', id: 'bulk-room-assign-modal');
        $this->form->fill([
            'reservations' => $this->reservations
        ]);
    }

    public function form(Form $form): Form
    {
        $roomTypes = static::roomTypes();

        return $form
            ->schema([
                Forms\Components\Repeater::make('reservations')
                    ->label('')
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\DatePicker::make('from')
                                ->label('Check-In')
                                ->format('d/m/Y')
                                ->live()
                                ->required(),
                            Forms\Components\DatePicker::make('to')
                                ->label('Check-Out')
                                ->format('d/m/Y')
                                ->live()
                                ->required(),

                            Forms\Components\Select::make('room_type_id')
                                ->options($roomTypes->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->live(),

                            Forms\Components\Select::make('rate_plan_id')
                                ->options(fn($get) => $get('room_type_id') ? $roomTypes->where('id', $get('room_type_id'))->first()->ratePlans->pluck('name', 'id') : [])
                                ->required()
                                ->searchable()
                                ->afterStateUpdated(function ($get, $set) use ($roomTypes) {})
                                ->live(),

                            Forms\Components\Select::make('room_id')
                                ->options(function ($get, $set): Collection {
                                    if ($get('from')) {
                                        $reservations = BookingReservation::query()
                                            ->where('room_type_id', $get('room_type_id'))
                                            ->where(function ($query) use ($get) {
                                                $query->where('from', '<=', $get('to'))
                                                    ->where('to', '>=', $get('from'));
                                            })
                                            ->whereNotNull('room_id')
                                            ->get();
                                        return Room::query()
                                            ->where('room_type_id', $get('room_type_id'))
                                            ->whereNotIn('id', $reservations->pluck('room_id'))
                                            ->pluck('room_number', 'id');
                                    }
                                    return collect([]);
                                })
                                ->required()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->live(),

                        ])->columns(5),
                    ])

                    ->required()
                    ->deletable(false)
                    ->addable(false)
                    ->itemLabel('Reservation'),

            ])
            ->statePath('data');
    }


    public function save()
    {
        foreach ($this->form->getState()['reservations'] as $reservationArray) {
            $reservation = BookingReservation::where('id', $reservationArray['id'])
                ->update([
                    'room_id' => $reservationArray['room_id'],
                    'rate_plan_id' => $reservationArray['rate_plan_id'],
                    'room_type_id' => $reservationArray['room_type_id'],
                ]);
        };

        Notification::make()
            ->title('Rooms assigned successfullly')
            ->success()
            ->send();

        $this->dispatch('refresh-scheduler');

        $this->dispatch('close-bulk-room-assign-modal', id: 'bulk-room-assign-modal');
    }


    public function render()
    {
        return view('livewire.pms.reservation.bulk-room-assign');
    }
}
