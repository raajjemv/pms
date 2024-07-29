<?php

namespace App\Filament\Resources\RoomClassResource\Pages;

use App\Filament\Resources\RoomClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRoomClass extends ViewRecord
{
    protected static string $resource = RoomClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
