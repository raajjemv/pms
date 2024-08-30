<?php

namespace App\Filament\App\Resources\ChannelGroupResource\Pages;

use App\Filament\App\Resources\ChannelGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewChannelGroup extends ViewRecord
{
    protected static string $resource = ChannelGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
