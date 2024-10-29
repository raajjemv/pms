<?php

namespace App\Filament\Resources\BedTypeResource\Pages;

use Filament\Actions;
use App\Models\BedType;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BedTypeResource;

class ListBedTypes extends ListRecords
{
    protected static string $resource = BedTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Load Default Bed Types')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => !static::getResource()::getEloquentQuery()->count())
                ->action(function () {
                    $tenant = Filament::getTenant()->id;
                    $user_id = auth()->id();
                    $bedTypeData = [
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'King'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Queen'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Double'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Twin'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Single'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Bunk Bed'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Sofa Bed'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Waterbed'],
                    ];
                    
                    BedType::insert($bedTypeData);
                }),
        ];
    }
}
