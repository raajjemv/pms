<?php

namespace App\Filament\Resources\RoomClassResource\Pages;

use App\Filament\Resources\RoomClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomClasses extends ListRecords
{
    protected static string $resource = RoomClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
