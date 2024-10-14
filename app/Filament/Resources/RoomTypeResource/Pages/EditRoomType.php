<?php

namespace App\Filament\Resources\RoomTypeResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Cache;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\RoomTypeResource;
use Filament\Facades\Filament;

class EditRoomType extends EditRecord
{
    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave()
    {
        Cache::forget('room_type_' . $this->record->id);
        Cache::forget('room_types_' . Filament::getTenant()->id);
    }
}
