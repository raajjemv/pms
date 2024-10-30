<?php

namespace App\Filament\Clusters\Configurations\Resources\VoidReasonResource\Pages;

use App\Filament\Clusters\Configurations\Resources\VoidReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVoidReason extends EditRecord
{
    protected static string $resource = VoidReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn($record) => !$record->locked),

        ];
    }
}
