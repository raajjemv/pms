<?php

namespace App\Filament\Resources\RoomStatusResource\Pages;

use App\Filament\Resources\RoomStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomStatuses extends ListRecords
{
    protected static string $resource = RoomStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
