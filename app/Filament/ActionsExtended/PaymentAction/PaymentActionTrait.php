<?php

namespace App\Filament\ActionsExtended\PaymentAction;

use Filament\Forms;
use App\Enums\PaymentType;
use App\Http\Traits\CachedQueries;
use App\Models\BookingTransaction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;

trait PaymentActionTrait
{
    use CachedQueries;

    public static function getDefaultName(): ?string
    {
        return 'add-payment';
    }

    protected function setUp(): void
    {
        $businessSources = static::businessSources();

        parent::setUp();

        $this
            ->modalSubmitActionLabel('Add')
            ->icon('heroicon-m-currency-dollar')
            ->color('gray')
            ->fillForm(fn($livewire): array => [
                'rate' => reservationTotals($livewire->selectedFolio->id)['balance']
            ])
            ->mutateFormDataUsing(function (array $data, $livewire): array {
                $data['booking_id'] = $livewire->booking->id;
                $data['user_id'] = auth()->id();
                $data['booking_reservation_id'] = $livewire->selectedFolio->id;
                return $data;
            })
            ->modalWidth(MaxWidth::Small)
            ->form([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('rate')
                    ->label('Amount')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('transaction_type')
                    ->options(PaymentType::class)
                    ->required()
                    ->live(),

                Forms\Components\Select::make('business_source_id')
                    ->label('Business Source')
                    ->options($businessSources->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn($get) => $get('transaction_type') == 'city_ledger')
                    ->required(fn($get) => $get('transaction_type') == 'city_ledger' ? true : false),

            ]);


        $this->action(function ($data, $livewire): void {
            BookingTransaction::create($data);
        });

        $this->after(function ($livewire, $data) {
            Cache::forget('reservationBalance_' . $livewire->selectedFolio->id);
            activity()->performedOn($livewire->selectedFolio)->log('Payment of ' . $data['rate'] . ' collected by ' . $data['transaction_type']);
            $livewire->dispatch('refresh-edit-reservation');
            Notification::make()
                ->title('Payment successful!')
                ->success()
                ->send();
        });
    }
}
