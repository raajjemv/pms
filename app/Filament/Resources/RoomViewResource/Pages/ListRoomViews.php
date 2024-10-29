<?php

namespace App\Filament\Resources\RoomViewResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RoomViewResource;
use App\Models\RoomView;

class ListRoomViews extends ListRecords
{
    protected static string $resource = RoomViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Load Default Room Views')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => !static::getResource()::getEloquentQuery()->count())
                ->action(function () {
                    $tenant = Filament::getTenant()->id;
                    $user_id = auth()->id();
                    $roomViewData = [
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'City'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Ocean'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Mountain'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Garden'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Pool'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Courtyard'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Street'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Interior'],
                    ];

                    RoomView::insert($roomViewData);
                }),
        ];
    }
}
