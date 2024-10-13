<?php

namespace App\Filament\Resources\BusinessSourceResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BusinessSourceResource;

class CreateBusinessSource extends CreateRecord
{
    protected static string $resource = BusinessSourceResource::class;

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
        Cache::forget('business_sources_' . $tenant);
    }
}
