<?php

namespace App\Filament\Resources\RoomClassResource\Pages;

use App\Filament\Resources\RoomClassResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomClass extends EditRecord
{
    protected static string $resource = RoomClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
