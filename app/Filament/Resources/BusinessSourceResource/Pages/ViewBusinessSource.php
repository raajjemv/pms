<?php

namespace App\Filament\Resources\BusinessSourceResource\Pages;

use App\Filament\Resources\BusinessSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBusinessSource extends ViewRecord
{
    protected static string $resource = BusinessSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
