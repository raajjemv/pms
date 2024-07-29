<?php

namespace App\Filament\Resources\RoomViewResource\Pages;

use App\Filament\Resources\RoomViewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomView extends EditRecord
{
    protected static string $resource = RoomViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
