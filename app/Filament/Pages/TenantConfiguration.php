<?php

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms;

class TenantConfiguration extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.tenant-configuration';

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill(Filament::getTenant()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->required(),
                    Forms\Components\TextInput::make('phone_number')
                        ->required(),

                    Forms\Components\TextInput::make('bill_initials')
                        ->required(),
                    Forms\Components\Textarea::make('address')
                        ->required(),
                    Forms\Components\TextInput::make('tin')
                        ->required(),
                    Forms\Components\FileUpload::make('logo')
                        ->image()
                        ->imageEditor()
                        ->optimize('webp')
                        ->disk(env('FILESYSTEM_DISK'))
                        ->required(),
                ])
                    ->columns(2)
            ])
            ->statePath('data');
    }

    public function save()
    {
        $tenant = Filament::getTenant()->update($this->form->getState());
    }
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user->allRoles()->whereName('admin')->exists() || $user->hasRole('tenant_owner|hotel_manager');
    }
}
