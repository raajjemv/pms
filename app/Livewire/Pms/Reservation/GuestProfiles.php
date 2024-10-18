<?php

namespace App\Livewire\Pms\Reservation;

use Filament\Forms;
use Filament\Tables;
use App\Models\Country;
use Livewire\Component;
use App\Models\Customer;
use Filament\Tables\Table;
use App\Enums\DocumentType;
use Livewire\Attributes\On;
use Filament\Tables\Actions;
use Filament\Facades\Filament;
use Livewire\Attributes\Reactive;
use App\Models\BookingReservation;
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

    #[On('refresh-edit-reservation')]
    public function refreshComponent() {}


    public function table(Table $table): Table
    {


        return $table
            ->relationship(fn(): BelongsToMany => $this->booking->customers()->wherePivot('booking_reservation_id', $this->selectedFolio->id))
            ->columns([

                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('document_number'),
                Tables\Columns\IconColumn::make('pivot.master')
                    ->label('Master')
                    ->boolean(),
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
                                'booking_email' => $this->booking->booking_email,
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
                Actions\Action::make('Set as Master')
                    ->icon('heroicon-m-user')
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $reservation = BookingReservation::find($record->booking_reservation_id);

                        $reservation->booking_customer = $record->name;
                        $reservation->save();

                        $reservation->customers()->newPivotQuery()->update([
                            'master' => false
                        ]);
                        $reservation->customers()->updateExistingPivot($record->id, [
                            'master' => true
                        ]);
                    })
                    ->after(function ($livewire) {
                        $this->dispatch('refresh-edit-reservation');
                    })
                    ->visible(fn($record) => !$record->master),
                Actions\EditAction::make()
                    ->modalWidth(MaxWidth::SevenExtraLarge)
                    ->form([
                        ...static::guestRegistrationFields()
                    ])
                    ->after(function ($record,$livewire) {
                        if ($record->pivot->master) {
                            $reservation = BookingReservation::where('id', $this->selectedFolio->id)
                                ->update([
                                    'booking_customer' => $record->name
                                ]);
                        };
                        $livewire->dispatch('refresh-edit-reservation');
                    }),
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
