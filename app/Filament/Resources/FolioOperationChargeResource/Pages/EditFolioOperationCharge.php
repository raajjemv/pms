<?php

namespace App\Filament\Resources\FolioOperationChargeResource\Pages;

use App\Filament\Resources\FolioOperationChargeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFolioOperationCharge extends EditRecord
{
    protected static string $resource = FolioOperationChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
