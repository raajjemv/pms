<?php

namespace App\Filament\Clusters\Configurations\Resources\CancelReasonResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Configurations\Resources\CancelReasonResource;

class CreateCancelReason extends CreateRecord
{
    protected static string $resource = CancelReasonResource::class;
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

    protected function afterCreate()
    {
        $tenant = Filament::getTenant()->id;
    }
}
