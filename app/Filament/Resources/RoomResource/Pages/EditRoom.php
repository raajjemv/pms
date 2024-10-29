<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use App\Http\Traits\CachedQueries;
use App\Models\Room;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditRoom extends EditRecord
{
    use CachedQueries;

    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Attach Connecting Rooms')
                ->fillForm(function ($record) {
                    $rooms = $record->family_room_id ? Room::query()
                        ->where('family_room_id', $record->family_room_id)
                        ->where('id', '!=', $record->id)
                        ->pluck('id') : [];
                    return [
                        'rooms' => $rooms
                    ];
                })
                ->modalWidth('sm')
                ->form([
                    Forms\Components\Select::make('rooms')
                        ->searchable()
                        ->preload()
                        ->multiple()
                        ->options(fn($record) => static::rooms()->where('id', '!=', $record->id)->pluck('room_number', 'id'))
                       
                ])
                ->action(function ($data, $record) {
                    $connecting_id = str()->random(10);

                    collect($data['rooms'])->each(function ($roomid) use ($connecting_id) {
                        $room = Room::where('id', $roomid)->update([
                            'family_room_id' => $connecting_id
                        ]);
                    });
                    $record->family_room_id = $connecting_id;
                    $record->save();
                })
                ->visible(fn($record) => $record->family_room),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
