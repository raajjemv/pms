<?php

namespace App\Filament\Resources\RoomClassResource\Pages;

use Filament\Actions;
use App\Models\RoomClass;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RoomClassResource;

class ListRoomClasses extends ListRecords
{
    protected static string $resource = RoomClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Load Default Room Classes')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => !static::getResource()::getEloquentQuery()->count())
                ->action(function () {
                    $tenant = Filament::getTenant()->id;
                    $user_id = auth()->id();
                    $roomClassData = [
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Standard'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Deluxe'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Superior'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Luxury'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Premium'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Budget'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Economy'],
                    ];

                    RoomClass::insert($roomClassData);
                }),
        ];
    }
}
