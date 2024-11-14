<?php

namespace App\Livewire\Pms\Reservation;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use Livewire\Attributes\Reactive;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class ModifyReservation extends Component implements HasForms
{
    use InteractsWithForms;
    use CachedQueries;

    public $booking;

    #[Reactive]
    public $selectedFolio;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'room_type' => $this->selectedFolio->room_type_id,
            'rate_plan_id' => $this->selectedFolio->rate_plan_id,
            'adults' => $this->selectedFolio->adults,
            'children' => $this->selectedFolio->children,
        ]);
    }

    public function form(Form $form): Form
    {
        $roomTypes = static::roomTypes();

        return $form
            ->schema([
                Forms\Components\Select::make('room_type')
                    ->options($roomTypes->pluck('name', 'id'))
                    ->searchable()
                    ->disabled()
                    ->live(),

                Forms\Components\Select::make('rate_plan_id')
                    ->label('Rate Plan')
                    ->options(fn($get) => $get('room_type') ? $roomTypes->where('id', $get('room_type'))->first()->ratePlans->pluck('name', 'id') : [])
                    ->required()
                    ->searchable()
                    ->afterStateUpdated(function ($get, $set) use ($roomTypes) {})
                    ->live(),
                Forms\Components\TextInput::make('adults')
                    ->numeric()
                    ->required()
                    ->default(2)
                    ->live(),
                Forms\Components\TextInput::make('children')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->live(),
            ])
            ->columns(2)
            ->statePath('data')
            ->model($this->booking);
    }

    public function saveReservationDetail()
    {
        BookingReservation::where('id', $this->selectedFolio->id)
            ->update($this->form->getState());
        $this->dispatch('refresh-edit-reservation');

        Notification::make()
            ->title('Reservation Updated!')
            ->success()
            ->send();
    }


    public function render()
    {
        return view('livewire.pms.reservation.modify-reservation');
    }
}
