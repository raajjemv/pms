<?php

namespace App\Filament\ActionsExtended\RoomAssignAction;

use Closure;
use Filament\Forms;
use App\Models\Room;
use App\Enums\Status;
use Filament\Facades\Filament;
use App\Models\RoomAssignReason;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use App\Models\BookingTransaction;
use Illuminate\Support\Collection;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Filament\Widgets\StatsOverviewWidget\Stat;

trait RoomAssignActionTrait
{
    use CachedQueries;

    public static function getDefaultName(): ?string
    {
        return 'assign-room';
    }

    protected function setUp(): void
    {

        parent::setUp();

        $roomTypes = static::roomTypes();

        $this
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
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->live()
                    ])

                ];
            })
            ->visible(fn($record) => is_null($record->room_id));


        $this->action(function ($record, $data): void {
            $record->update([
                'room_id' => $data['room'],
                'rate_plan_id' => $data['rate_plan'],
                'room_type_id' => $data['room_type'],

            ]);
        });

        $this->after(function ($livewire, $data) {
            $this->dispatch('refresh-reservations');
            $this->dispatch('refresh-scheduler');
            Notification::make()
                ->title('Room Assigned successful')
                ->success()
                ->send();
        });
    }
}
