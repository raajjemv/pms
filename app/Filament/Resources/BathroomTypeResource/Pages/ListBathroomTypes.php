<?php

namespace App\Filament\Resources\BathroomTypeResource\Pages;

use Filament\Actions;
use App\Models\BathroomType;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BathroomTypeResource;

class ListBathroomTypes extends ListRecords
{
    protected static string $resource = BathroomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Load Default Bathroom Types')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => !static::getResource()::getEloquentQuery()->count())
                ->action(function () {
                    $tenant = Filament::getTenant()->id;
                    $user_id = auth()->id();
                    $bathroomTypeData = [
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Private'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Shared'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Ensuite'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Attached'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Jack and Jill'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Half Bath'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Accessible Bathroom'],
                    ];
                    
                    BathroomType::insert($bathroomTypeData);
                }),
        ];
    }
}
