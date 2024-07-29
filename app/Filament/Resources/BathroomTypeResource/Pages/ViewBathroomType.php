<?php

namespace App\Filament\Resources\BathroomTypeResource\Pages;

use App\Filament\Resources\BathroomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBathroomType extends ViewRecord
{
    protected static string $resource = BathroomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
