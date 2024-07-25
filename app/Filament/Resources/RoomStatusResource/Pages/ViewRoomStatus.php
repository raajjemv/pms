<?php

namespace App\Filament\Resources\RoomStatusResource\Pages;

use App\Filament\Resources\RoomStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRoomStatus extends ViewRecord
{
    protected static string $resource = RoomStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
