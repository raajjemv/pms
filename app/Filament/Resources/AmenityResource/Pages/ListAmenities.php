<?php

namespace App\Filament\Resources\AmenityResource\Pages;

use App\Filament\Resources\AmenityResource;
use App\Models\Amenity;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListAmenities extends ListRecords
{
    protected static string $resource = AmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Load Default Amenities')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => !static::getResource()::getEloquentQuery()->count())
                ->action(function () {
                    $tenant = Filament::getTenant()->id;
                    $user_id = auth()->id();
                    $amenityData = [
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Air conditioning'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'TV'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Wi-Fi'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Mini-bar'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Safe'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Hairdryer'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Iron and ironing board'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Tea/coffee maker'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Refrigerator'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Balcony/terrace'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Jacuzzi/hot tub'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Kitchenette'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Kitchen'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Spa bath'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Sauna'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Steam room'],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Fitness equipment'],
                    ];

                    Amenity::insert($amenityData);
                }),
        ];
    }
}
