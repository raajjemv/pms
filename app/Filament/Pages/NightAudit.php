<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class NightAudit extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static ?string $navigationGroup = 'Reporting';

    protected static string $view = 'filament.pages.night-audit';
}
