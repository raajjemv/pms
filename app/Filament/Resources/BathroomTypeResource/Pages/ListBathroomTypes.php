<?php

namespace App\Filament\Resources\BathroomTypeResource\Pages;

use App\Filament\Resources\BathroomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBathroomTypes extends ListRecords
{
    protected static string $resource = BathroomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
