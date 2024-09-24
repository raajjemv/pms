<?php

namespace App\Livewire\Pms\Reservation;

use Filament\Forms;
use Filament\Tables;
use App\Models\Country;
use Livewire\Component;
use App\Models\Customer;
use Filament\Tables\Table;
use App\Enums\DocumentType;
use Filament\Tables\Actions;
use Filament\Facades\Filament;
use Livewire\Attributes\Reactive;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Http\Traits\InteractsWithGuestRegistration;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GuestProfiles extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithGuestRegistration;
    use InteractsWithActions;

    public $booking;


    #[Reactive]
    public $selectedFolio;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function table(Table $table): Table
    {


        return $table
            ->relationship(fn(): BelongsToMany => $this->booking->customers()->wherePivot('booking_reservation_id', $this->selectedFolio->id))
            ->columns([

                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('document_number'),
                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label('Registerd Date')
                    ->dateTime('M d, Y H:i')

            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                Actions\Action::make('Initiate Guest')
                    ->label($this->booking->customers()->wherePivot('booking_reservation_id', $this->selectedFolio->id)->count() ? 'Add Sharer' : 'Initiate Guest')
                    ->modalWidth(MaxWidth::Small)
                    ->form([
                        Forms\Components\Radio::make('registration_type')
                            ->options([
                                'new_guest' => 'New Guest Registration',
                                'returning_guest' => 'Returning Guest',
                            ])
                            ->inline()
                            ->required()
                    ])
                    ->action(function ($data, $arguments) {
                        $registration_type = $data['registration_type'];
                        match ($registration_type) {
                            'new_guest' => $this->replaceMountedAction('newRegistration', [
                                'booking_customer' => $this->booking->booking_customer,
                                'booking' => $this->booking->id,
                                'booking_reservation_id' => $this->selectedFolio->id
                            ]),
                            'returning_guest' => $this->replaceMountedAction('returningGuest', [
                                'booking' => $this->booking->id,
                                'booking_reservation_id' => $this->selectedFolio->id

                            ])
                        };
                    }),


            ])
            ->actions([
                Actions\EditAction::make()
                    ->form([
                        Forms\Components\Fieldset::make('Profile')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->disk(env('FILESYSTEM_DISK'))
                                    ->panelAspectRatio('2:1')
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

                    ]),
                Actions\DetachAction::make()
                    ->label('Void')
            ])
            ->bulkActions([
                // ...
            ])
            ->paginated(false)
            ->striped();
    }

    public function render()
    {
        return view('livewire.pms.reservation.guest-profiles');
    }
}
