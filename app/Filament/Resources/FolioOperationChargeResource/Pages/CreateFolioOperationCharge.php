<?php

namespace App\Filament\Resources\FolioOperationChargeResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\FolioOperationChargeResource;
use Illuminate\Support\Facades\Cache;

class CreateFolioOperationCharge extends CreateRecord
{
    protected static string $resource = FolioOperationChargeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Filament::getTenant()->id;
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate()
    {
        Cache::forget('folio_operation_charges_' . Filament::getTenant()->id);
    }


    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
