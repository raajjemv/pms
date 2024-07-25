<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('Detach User')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->tenants()->detach(Filament::getTenant()->id);
                    return redirect(static::getResource()::getUrl('index'));
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
