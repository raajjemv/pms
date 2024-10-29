<?php

namespace App\Filament\Resources\RatePlanResource\Pages;

use Filament\Actions;
use App\Models\RatePlan;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RatePlanResource;

class ListRatePlans extends ListRecords
{
    protected static string $resource = RatePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Load Default Rate Plans')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => !static::getResource()::getEloquentQuery()->count())
                ->action(function () {
                    $tenant = Filament::getTenant()->id;
                    $user_id = auth()->id();
                    $ratePlanData = [
                        [
                            'tenant_id' => $tenant,
                            'name' => 'Room Only',
                            'code' => 'RO',
                            'user_id' => $user_id,
                        ],
                        [
                            'tenant_id' => $tenant,
                            'name' => 'Bed & Breakfast',
                            'code' => 'BB',
                            'user_id' => $user_id,
                        ],
                        [
                            'tenant_id' => $tenant,
                            'name' => 'Half Board',
                            'code' => 'HB',
                            'user_id' => $user_id,
                        ],
                        [
                            'tenant_id' => $tenant,
                            'name' => 'Full Board',
                            'code' => 'FB',
                            'user_id' => $user_id,
                        ],
                    ];

                    RatePlan::insert($ratePlanData);
                }),
        ];
    }
}
