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
use Filament\Actions\CreateAction;
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
                    // ->options(Customer::selectRaw('CONCAT(name, " - ", document_number) AS name, id')->limit(5)->pluck('name', 'id'))
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
            ->fillForm(function ($arguments) {
                $reservation = BookingReservation::with('customers')->find($arguments['booking_reservation_id']);
                if ($reservation->customers->count() < 1) {
                    return [
                        'master' => true
                    ];
                }
            })
            ->form([
                Forms\Components\Select::make('guest')
                    // ->options(Customer::selectRaw('CONCAT(name, " - ", document_number) AS name, id')->limit(5)->pluck('name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => Customer::where('name', 'like', "%{$search}%")->orWhere('document_number', 'like', "%{$search}%")->selectRaw('CONCAT(name, " - ", document_number) AS name, id')->limit(5)->pluck('name', 'id')->toArray())
                    ->placeholder('Search by name or document number')
                    ->preload(false),

                Forms\Components\Toggle::make('master')

            ])
            ->action(function ($data, $arguments) {
                $reservation = BookingReservation::with('customers')->find($arguments['booking_reservation_id']);

                $customer = Customer::find($data['guest']);

                if ($data['master'] == true) {
                    $reservation->booking_customer = $customer->name;
                    $reservation->save();
                    $reservation->customers()->newPivotQuery()->update([
                        'master' => false
                    ]);
                }

                $reservation->customers()->attach($data['guest'], [
                    'booking_id' => $arguments['booking'],
                    'master' => $data['master']
                ]);
            })
            ->after(function ($livewire) {
                $livewire->dispatch('refresh-edit-reservation');
            });
    }
    public static function newRegistration(): Action
    {
        return CreateAction::make('new-registration')
            ->model(Customer::class)
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
                ...self::guestRegistrationFields()
            ])
            ->using(function ($data, $arguments,$model) {
                $reservation = BookingReservation::find($arguments['booking_reservation_id']);

                $customer_count = $reservation->customers->count();

                $master = $customer_count < 1 ? true : false;

                if ($customer_count < 1) {
                    $reservation->booking_customer =  $data['name'];
                    $reservation->save();
                }


                $customer = $model::firstOrCreate($data);

                $reservation->customers()->attach($customer, [
                    'booking_id' => $arguments['booking'],
                    'master' => $master
                ]);
            })
            ->after(function ($livewire) {
                $livewire->dispatch('refresh-edit-reservation');
            });
    }

    public static function guestRegistrationFields()
    {
        return  [
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
                        ->relationship('country','name')
                        // ->options(fn() => static::countries()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->optionsLimit(5),

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
                ->columns(3)
        ];
    }
}
