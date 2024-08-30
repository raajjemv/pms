<?php

namespace App\Filament\App\Resources\ChannelResource\Pages;

use App\Filament\App\Resources\ChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChannel extends EditRecord
{
    protected static string $resource = ChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
