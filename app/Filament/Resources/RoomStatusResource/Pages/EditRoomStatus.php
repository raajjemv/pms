<?php

namespace App\Filament\Resources\RoomStatusResource\Pages;

use App\Filament\Resources\RoomStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomStatus extends EditRecord
{
    protected static string $resource = RoomStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
