<?php

namespace App\Filament\Resources\BusinessSourceResource\Pages;

use App\Filament\Resources\BusinessSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessSource extends EditRecord
{
    protected static string $resource = BusinessSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
