<?php

namespace App\Filament\App\Resources\ChannelGroupResource\Pages;

use App\Filament\App\Resources\ChannelGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChannelGroup extends EditRecord
{
    protected static string $resource = ChannelGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
