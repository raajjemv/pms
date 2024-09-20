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
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Http\Traits\InteractsWithGuestRegistration;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GuestProfiles extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithGuestRegistration;
    use InteractsWithActions;

    public $booking;

    public $guestFound = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function table(Table $table): Table
    {


        return $table
            ->relationship(fn(): BelongsToMany => $this->booking->customers())
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
                    ->label($this->booking->customers->count() ? 'Add Sharer' : 'Initiate Guest')
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
                                'booking' => $this->booking->id
                            ]),
                            'returning_guest' => $this->replaceMountedAction('returningGuest', [
                                'booking' => $this->booking->id
                            ])
                        };
                    }),


            ])
            ->actions([
                Actions\DetachAction::make()
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
