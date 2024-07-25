<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate()
    {
        // $this->record->tenants()->attach(Filament::getTenant()->id);
    }
}
