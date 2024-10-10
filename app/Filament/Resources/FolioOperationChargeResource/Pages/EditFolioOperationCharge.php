<?php

namespace App\Filament\Resources\FolioOperationChargeResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\FolioOperationChargeResource;

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

    protected function afterSave(): void
    {
        Cache::forget('folio_operation_charges_' . Filament::getTenant()->id);
    }
}
