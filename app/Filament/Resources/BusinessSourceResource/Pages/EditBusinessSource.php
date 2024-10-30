<?php

namespace App\Filament\Resources\BusinessSourceResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Cache;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\BusinessSourceResource;
use Filament\Facades\Filament;

class EditBusinessSource extends EditRecord
{
    protected static string $resource = BusinessSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn($record) => !$record->locked),
        ];
    }

    protected function afterSave()
    {
        $tenant = Filament::getTenant()->id;
        Cache::forget('business_sources_' . $tenant);
    }
}
