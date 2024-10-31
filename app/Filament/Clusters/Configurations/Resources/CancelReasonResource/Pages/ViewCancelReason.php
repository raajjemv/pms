<?php

namespace App\Filament\Clusters\Configurations\Resources\CancelReasonResource\Pages;

use App\Filament\Clusters\Configurations\Resources\CancelReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCancelReason extends ViewRecord
{
    protected static string $resource = CancelReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
