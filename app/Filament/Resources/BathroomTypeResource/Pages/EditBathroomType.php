<?php

namespace App\Filament\Resources\BathroomTypeResource\Pages;

use App\Filament\Resources\BathroomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBathroomType extends EditRecord
{
    protected static string $resource = BathroomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
