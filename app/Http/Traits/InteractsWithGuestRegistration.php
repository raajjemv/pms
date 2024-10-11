<?php

namespace App\Http\Traits;

use Filament\Forms;
use App\Models\Booking;
use App\Models\Country;
use Filament\Forms\Set;
use App\Models\Customer;
use App\Enums\DocumentType;
use App\Filament\Resources\BookingResource;
use App\Models\BookingReservation;
use Filament\Actions\Action;
use Filament\Tables\Actions;
use Filament\Actions\EditAction;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\ActionSize;

trait InteractsWithGuestRegistration
{
    use CachedQueries;


    public static function addFolioAccountAction(): Action
    {

        return Action::make('addFolioAccountAction')
            ->label('')
            ->icon('heroicon-m-user-plus')
            ->iconButton()
            ->size(ActionSize::Small)
            ->requiresConfirmation()
            ->modalWidth(MaxWidth::Small)
            ->color('gray')
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
                $booking->customers()->attach($data['guest'], [
                    'booking_reservation_id' => $arguments['booking_reservation_id']
                ]);
            });
    }

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
                $booking->customers()->attach($data['guest'], [
                    'booking_reservation_id' => $arguments['booking_reservation_id']
                ]);
            });
    }
    public static function newRegistration(): Action
    {
        return Action::make('new-registration')
            ->modalWidth(MaxWidth::SevenExtraLarge)
            ->fillForm(fn($arguments): array => [
                'name' => $arguments['booking_customer'],
                'email' => $arguments['booking_email'],
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
                $reservation = BookingReservation::where('id', $arguments['booking_reservation_id'])
                    ->update([
                        'booking_customer' => $data['name']
                    ]);
                $customer = Customer::firstOrCreate($data);
                $booking->customers()->attach($customer, [
                    'booking_reservation_id' => $arguments['booking_reservation_id']
                ]);
            })
            ->after(function ($livewire) {
                $livewire->dispatch('refresh-edit-reservation');
            });
    }

    public static function editRegistration(): Action
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
                $booking->customers()->attach($customer, [
                    'booking_reservation_id' => $arguments['booking_reservation_id']
                ]);
            });
    }
}
