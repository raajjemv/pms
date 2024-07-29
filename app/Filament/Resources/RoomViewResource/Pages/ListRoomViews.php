<?php

namespace App\Filament\Resources\RoomViewResource\Pages;

use App\Filament\Resources\RoomViewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomViews extends ListRecords
{
    protected static string $resource = RoomViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
