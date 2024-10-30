<?php

namespace App\Filament\Clusters\Configurations\Resources\VoidReasonResource\Pages;

use App\Filament\Clusters\Configurations\Resources\VoidReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVoidReason extends ViewRecord
{
    protected static string $resource = VoidReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
