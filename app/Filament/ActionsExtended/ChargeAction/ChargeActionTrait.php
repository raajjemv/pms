<?php

namespace App\Filament\ActionsExtended\ChargeAction;

use Filament\Forms;
use App\Http\Traits\CachedQueries;
use App\Models\BookingTransaction;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;

trait ChargeActionTrait
{
    use CachedQueries;
    
    public static function getDefaultName(): ?string
    {
        return 'add-charge';
    }

    protected function setUp(): void
    {

        parent::setUp();

        $folioOperationCharges = static::folioOperationCharges();

        $this
            ->icon('heroicon-m-plus-circle')
            ->color('gray')
            ->mutateFormDataUsing(function (array $data,$livewire) use ($folioOperationCharges) {
                $data['booking_id'] = $livewire->booking->id;
                $data['transaction_type'] = $folioOperationCharges->where('id', $data['transaction_type'])->first()->name;
                $data['user_id'] = auth()->id();
                $data['booking_reservation_id'] = $livewire->selectedFolio->id;
                return $data;
            })
            ->modalWidth(MaxWidth::Small)
            ->form([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Select::make('transaction_type')
                    ->label('Charge')
                    ->options($folioOperationCharges->pluck('name', 'id'))
                    ->afterStateUpdated(fn($set, $state) => $set('rate', $folioOperationCharges->where('id', $state)->first()->rate))
                    ->live(),
                \LaraZeus\Quantity\Components\Quantity::make('quantity')
                    ->dehydrated(false)
                    ->default(1)
                    ->maxValue(10)
                    ->minValue(1)
                    ->afterStateUpdated(fn($get, $set, $state) => $set('rate', $folioOperationCharges->where('id', $get('transaction_type'))->first()->rate * $state))
                    ->visible(fn($get) => $get('transaction_type')),
                Forms\Components\TextInput::make('rate')
                    ->label('Amount')
                    ->numeric()
                    ->visible(fn($get) => $get('transaction_type')),


            ]);


        $this->action(function ($data): void {
            BookingTransaction::create($data);
        });

        $this->after(function ($livewire, $data) {
            Cache::forget('reservationBalance_' . $livewire->selectedFolio->id);
            activity()->performedOn($livewire->selectedFolio)->log($data['rate'] . ' Charge Added');
            $livewire->dispatch('refresh-edit-reservation');
            Notification::make()
                ->title('Payment successful!')
                ->success()
                ->send();
        });
    }
}
