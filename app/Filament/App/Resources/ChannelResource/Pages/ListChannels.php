<?php

namespace App\Filament\App\Resources\ChannelResource\Pages;

use App\Filament\App\Resources\ChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChannels extends ListRecords
{
    protected static string $resource = ChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
