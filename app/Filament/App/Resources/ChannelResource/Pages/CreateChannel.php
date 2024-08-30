<?php

namespace App\Filament\App\Resources\ChannelResource\Pages;

use App\Filament\App\Resources\ChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChannel extends CreateRecord
{
    protected static string $resource = ChannelResource::class;


    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
