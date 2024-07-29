<?php

namespace App\Filament\Resources\RoomViewResource\Pages;

use App\Filament\Resources\RoomViewResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRoomView extends ViewRecord
{
    protected static string $resource = RoomViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
