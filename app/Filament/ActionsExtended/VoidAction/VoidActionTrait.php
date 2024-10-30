<?php

namespace App\Filament\ActionsExtended\VoidAction;

use Filament\Forms;
use App\Http\Traits\CachedQueries;
use App\Models\BookingTransaction;
use App\Models\VoidReason;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;

trait VoidActionTrait
{
    use CachedQueries;

    public static function getDefaultName(): ?string
    {
        return 'void-reservation';
    }

    protected function setUp(): void
    {

        parent::setUp();

        $reasons = VoidReason::pluck('reason', 'id');

        $this
            ->icon('heroicon-m-plus-circle')
            ->color('danger')
            ->requiresconfirmation()
            ->modalWidth(MaxWidth::Small)
            ->form([
                Forms\Components\Select::make('void_reason')
                    ->options(fn() => $reasons)
                    ->required()
                    ->suffixAction(
                        Action::make('create-void-reason')
                            ->icon('heroicon-m-plus-circle')
                            ->requiresConfirmation()
                            ->action(function ($set, $state) {
                              
                            })
                    )
            ]);


        $this->action(function ($data,$livewire): void {

        });

        $this->after(function ($livewire, $data) {});
    }
}
