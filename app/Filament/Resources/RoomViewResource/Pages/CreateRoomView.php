<?php

namespace App\Filament\Resources\RoomViewResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RoomViewResource;

class CreateRoomView extends CreateRecord
{
    protected static string $resource = RoomViewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
