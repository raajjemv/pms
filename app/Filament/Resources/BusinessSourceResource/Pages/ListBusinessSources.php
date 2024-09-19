<?php

namespace App\Filament\Resources\BusinessSourceResource\Pages;

use App\Filament\Resources\BusinessSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessSources extends ListRecords
{
    protected static string $resource = BusinessSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
