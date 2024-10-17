<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\Tenant;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class TenantConfiguration extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $view = 'filament.pages.tenant-configuration';

    protected static ?string $navigationGroup = 'System Settings';

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill(Filament::getTenant()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make('Business Information')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->required(),
                        Forms\Components\TextInput::make('phone_number')
                            ->required(),

                        Forms\Components\TextInput::make('bill_initials')
                            ->required(),
                        Forms\Components\Textarea::make('address')
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\TextInput::make('tin')
                            ->required(),
                        Forms\Components\TextInput::make('website')
                            ->nullable(),

                    ])->columns(2),
                    Forms\Components\Section::make('Settings')
                        ->schema([
                            Forms\Components\TextInput::make('usd_exchange_rate')
                                ->required(),
                            Forms\Components\TimePicker::make('check_in_time')
                                ->required(),
                            Forms\Components\TimePicker::make('check_out_time')
                                ->required(),
                            Forms\Components\TimePicker::make('late_check_out_time')
                                ->required(),
                            Forms\Components\TextInput::make('late_check_out_fee')
                                ->numeric()
                                ->minValue(0)
                                ->required(),
                        ])
                        ->columns(2)
                ])
                    ->columnSpan(2),
                Forms\Components\Section::make([
                    Forms\Components\FileUpload::make('logo')
                        ->image()
                        ->imageEditor()
                        ->optimize('webp')
                        ->disk(env('FILESYSTEM_DISK'))
                        ->required(),
                ])
                    ->columnSpan(1),

            ])->columns(3)
            ->statePath('data');
    }

    public function save()
    {
        $tenant = Filament::getTenant()->update($this->form->getState());

        Notification::make()
            ->title('Updated Successfully!')
            ->success()
            ->send();
    }
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }
}
