<?php

namespace App\Filament\ActionsExtended\MoveRoomAction;

use Filament\Forms;
use App\Models\Room;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use App\Models\BookingTransaction;
use Illuminate\Support\Collection;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;

trait MoveRoomActionTrait
{
    use CachedQueries;

    public static function getDefaultName(): ?string
    {
        return 'room-move';
    }

    protected function setUp(): void
    {

        parent::setUp();

        $roomTypes = static::roomTypes();

        $rooms = static::rooms();


        $this
            ->icon('heroicon-m-arrows-right-left')
            ->color('gray')
            ->modalWidth('sm')
            ->fillForm(fn($livewire) => [
                'room_type' => $rooms->where('id', $livewire->selectedFolio->room_id)->first()->room_type_id,
                'room' => $livewire->selectedFolio->room_id
            ])
            ->form([
                Forms\Components\Select::make('room_type')
                    ->options($roomTypes->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->live(),
                Forms\Components\Select::make('room')
                    ->options(function ($get, $livewire) use (&$rooms): Collection {
                        $room_type_id = $get('room_type');
                        $reservations = BookingReservation::query()
                            ->whereHas('room', function ($q) use ($room_type_id) {
                                $q->where('room_type_id', $room_type_id);
                            })
                            ->whereBetween('from', [$livewire->selectedFolio->from, $livewire->selectedFolio->to])
                            ->orWhereBetween('to', [$livewire->selectedFolio->from, $livewire->selectedFolio->to])
                            ->get();
                        return Room::query()
                            ->where('room_type_id', $get('room_type'))
                            ->whereNotIn('id', $reservations->pluck('room_id'))
                            ->pluck('room_number', 'id');
                    })
                    ->required()
                    ->live(),
                Forms\Components\Toggle::make('adjust_rate')
                    ->label('Adjust with new rates?')
                    ->default(1)
                    ->visible(fn($get, $livewire) => $get('room_type') !== $rooms->where('id', $livewire->selectedFolio->room_id)->first()->room_type_id)

            ])
            ->slideOver();


        $this->action(function ($data, $livewire): void {
            $reservation = BookingReservation::find($livewire->selectedFolio->id);
            $old_room = $reservation->room->room_number;
            $reservation->room_id = $data['room'];
            $reservation->save();

            $livewire->dispatch('refresh-scheduler');
            $livewire->dispatch('refresh-edit-reservation');
            Cache::forget('reservationBalance_' . $livewire->selectedFolio->id);
            activity()->performedOn($livewire->selectedFolio)->log("Room moved from {$old_room} to {$reservation->room->room_number}");
            Notification::make()
                ->title('Room moved successfull!')
                ->success()
                ->send();
        });

        $this->after(function ($livewire, $data) {});
    }
}
