<?php

namespace App\Filament\Resources\RoomTypeResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RoomTypeResource;
use App\Models\RoomType;

class ListRoomTypes extends ListRecords
{
    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Load Default Room Types')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => !static::getResource()::getEloquentQuery()->count())
                ->action(function () {
                    $tenant = Filament::getTenant()->id;
                    $user_id = auth()->id();
                    $roomTypeData = [
                        [
                            'tenant_id' => $tenant,
                            'user_id' => $user_id,
                            'name' => 'Single',
                            'description' => 'A room with a single bed for one person.',
                            'adults' => 2,
                            'children' => 0,

                        ],
                        [
                            'tenant_id' => $tenant,
                            'user_id' => $user_id,
                            'name' => 'Double',
                            'description' => 'A room with a double bed for two people.',
                            'adults' => 2,
                            'children' => 0,

                        ],
                        [
                            'tenant_id' => $tenant,
                            'user_id' => $user_id,
                            'name' => 'Twin',
                            'description' => 'A room with two single beds.',
                            'adults' => 2,
                            'children' => 0,

                        ],
                        [
                            'tenant_id' => $tenant,
                            'user_id' => $user_id,
                            'name' => 'Triple',
                            'description' => 'A room with three single beds or a combination of double and single bed.',
                            'adults' => 2,
                            'children' => 0,

                        ],
                        [
                            'tenant_id' => $tenant,
                            'user_id' => $user_id,
                            'name' => 'Executive Suite',
                            'description' => 'A high-end suite with additional amenities and services.',
                            'adults' => 2,
                            'children' => 0,

                        ],
                        [
                            'tenant_id' => $tenant,
                            'user_id' => $user_id,
                            'name' => 'Presidential Suite',
                            'description' => 'The most luxurious suite with exceptional amenities and services.',
                            'adults' => 2,
                            'children' => 0,

                        ],
                        [
                            'tenant_id' => $tenant,
                            'user_id' => $user_id,
                            'name' => 'Studio',
                            'description' => 'A single room with a kitchenette or cooking area.',
                            'adults' => 2,
                            'children' => 0,

                        ],
                        [
                            'tenant_id' => $tenant,
                            'user_id' => $user_id,
                            'name' => 'Family Room',
                            'description' => 'A room designed for families with multiple beds and space.',
                            'adults' => 2,
                            'children' => 0,

                        ]
                    ];

                    RoomType::insert($roomTypeData);
                }),
        ];
    }
}
