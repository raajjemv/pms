<?php

namespace App\Filament\Resources\FolioOperationChargeResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\FolioOperationChargeResource;
use App\Models\FolioOperationCharge;

class ListFolioOperationCharges extends ListRecords
{
    protected static string $resource = FolioOperationChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Load Default Charges')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => !static::getResource()::getEloquentQuery()->count())
                ->action(function () {
                    $tenant = Filament::getTenant()->id;
                    $user_id = auth()->id();
                    $folioCharges = [
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Airport Transfer', 'rate' => 30],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Extra Bed', 'rate' => 10],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Early Check-In', 'rate' => 30],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Late Check-Out', 'rate' => 30],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Spa Treatment', 'rate' => 50],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Laundry Service', 'rate' => 10],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Wi-Fi Charge', 'rate' => 5],
                        ['tenant_id' => $tenant, 'user_id' => $user_id, 'name' => 'Security Deposit', 'rate' => 100],
                    ];

                    FolioOperationCharge::insert($folioCharges);
                }),
        ];
    }
}
