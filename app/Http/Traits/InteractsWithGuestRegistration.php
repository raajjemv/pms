<?php

namespace App\Http\Traits;

use Filament\Forms;
use App\Models\Country;
use Filament\Forms\Set;
use App\Models\Customer;
use App\Enums\DocumentType;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Tables\Actions;
use Filament\Actions\EditAction;
use Filament\Support\Enums\MaxWidth;

trait InteractsWithGuestRegistration
{
    use CachedQueries;
    public static function returningGuest(): Action
    {

        return Action::make('existing-registration')
            ->modalWidth(MaxWidth::Small)
            ->form([
                Forms\Components\Select::make('guest')
                    ->options(Customer::selectRaw('CONCAT(name, " - ", document_number) AS name, id')->limit(5)->pluck('name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => Customer::where('name', 'like', "%{$search}%")->orWhere('document_number', 'like', "%{$search}%")->selectRaw('CONCAT(name, " - ", document_number) AS name, id')->limit(5)->pluck('name', 'id')->toArray())
                    ->placeholder('Search by name or document number')
                    ->preload(false)

            ])
            ->action(function ($data, $arguments) {
                $booking = Booking::find($arguments['booking']);
                $booking->customers()->attach($data['guest']);
            })
        ;
    }
    public static function newRegistration(): Action
    {
        return Action::make('new-registration')
            ->modalWidth(MaxWidth::SevenExtraLarge)
            ->fillForm(fn($arguments): array => [
                'name' => $arguments['booking_customer'],
            ])
            ->mutateFormDataUsing(function (array $data): array {
                $user = auth()->user();
                $data['tenant_id'] = $user->current_tenant_id;
                $data['user_id'] = $user->id;
                return $data;
            })
            ->form([
                Forms\Components\Fieldset::make('Profile')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->disk(env('FILESYSTEM_DISK'))
                            ->image()
                            ->optimize('webp'),
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('phone_number'),
                        Forms\Components\TextInput::make('email'),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female'
                            ])
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('Guest Type')
                            ->options([
                                'local' => 'Local',
                                'tourist' => 'Tourist',
                                'work_permit_holder' => 'Work Permit Holder'
                            ]),

                        Forms\Components\Select::make('country_id')
                            ->label('Country')
                            ->searchable()
                            ->required()
                            ->options(Country::pluck('name', 'id')),

                        Forms\Components\TextInput::make('address')
                            ->columnSpan(2),

                    ])
                    ->columns(3),
                Forms\Components\Fieldset::make('Identity Information')
                    ->schema([
                        Forms\Components\FileUpload::make('document_photo')
                            ->disk(env('FILESYSTEM_DISK'))
                            ->image()
                            ->optimize('webp'),

                        Forms\Components\Select::make('document_type')
                            ->options(DocumentType::class)
                            ->required(),
                        Forms\Components\TextInput::make('document_number')
                            ->required()
                    ])
                    ->columns(3),

            ])
            ->action(function ($data, $arguments) {
                $booking = Booking::find($arguments['booking']);
                $customer = Customer::firstOrCreate($data);
                $booking->customers()->attach($customer);
            });
    }
}
