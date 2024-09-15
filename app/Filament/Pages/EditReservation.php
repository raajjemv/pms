<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class EditReservation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.edit-reservation';

    public $activeTab = 'guest-accounting';
}
