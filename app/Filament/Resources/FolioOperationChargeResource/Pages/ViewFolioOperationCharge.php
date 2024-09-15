<?php

namespace App\Filament\Resources\FolioOperationChargeResource\Pages;

use App\Filament\Resources\FolioOperationChargeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFolioOperationCharge extends ViewRecord
{
    protected static string $resource = FolioOperationChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
