<?php

namespace App\Filament\Clusters\Configurations\Resources\CancelReasonResource\Pages;

use App\Filament\Clusters\Configurations\Resources\CancelReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCancelReason extends EditRecord
{
    protected static string $resource = CancelReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn($record) => !$record->locked),
        ];
    }
}
